<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Jmrashed\Zkteco\Lib\ZKTeco;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('user')->latest()->paginate(10);
        return view('attendance.index', compact('attendances'));
    }

    public function create()
    {
        $users = User::all();
        return view('attendance.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:Present,Absent,Late,Excused',
            'notes' => 'nullable|string',
        ]);

        Attendance::create($request->all());

        return redirect()->route('attendance.index')->with('success', 'Attendance recorded successfully.');
    }

    public function edit(Attendance $attendance)
    {
        $users = User::all();
        return view('attendance.edit', compact('attendance', 'users'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:Present,Absent,Late,Excused',
            'notes' => 'nullable|string',
        ]);

        $attendance->update($request->all());

        return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendance.index')->with('success', 'Attendance deleted successfully.');
    }

    public function sync(Request $request)
    {
        return redirect()->route('attendance.index')->with('error', 'Manual sync is disabled on the live server. Attendance is synced automatically via your office PC.');
    }

        $syncedCount = 0;
        $usersMap = User::all()->keyBy('id');

        foreach ($attendanceLog as $record) {
            $zktecoId = (int)$record['id']; 
            
            if (isset($usersMap[$zktecoId])) {
                $user = $usersMap[$zktecoId];
                $timestamp = Carbon::parse($record['timestamp']);
                $date = $timestamp->format('Y-m-d');
                $time = $timestamp->format('H:i:s');
                
                // 1. Save every punch to logs table (prevent duplicates with unique log_id)
                $logId = $user->id . '_' . $timestamp->format('YmdHis');
                \App\Models\AttendanceLog::firstOrCreate([
                    'log_id' => $logId
                ], [
                    'user_id' => $user->id,
                    'log_time' => $timestamp,
                ]);

                // 2. Fetch all logs for this user on this date to calculate summary
                $allLogs = \App\Models\AttendanceLog::where('user_id', $user->id)
                    ->whereDate('log_time', $date)
                    ->orderBy('log_time', 'asc')
                    ->get();

                if ($allLogs->count() > 0) {
                    $firstPunch = Carbon::parse($allLogs->first()->log_time);
                    $lastPunch = Carbon::parse($allLogs->last()->log_time);
                    
                    // Calculate Total Hours (Simple: Sum of intervals)
                    $totalMinutes = 0;
                    $logsArray = $allLogs->toArray();
                    for ($i = 0; $i < count($logsArray) - 1; $i += 2) {
                        $in = Carbon::parse($logsArray[$i]['log_time']);
                        $out = Carbon::parse($logsArray[$i+1]['log_time']);
                        $totalMinutes += $in->diffInMinutes($out);
                    }
                    $totalHours = round($totalMinutes / 60, 2);

                    // Update Attendance Summary
                    $attendance = Attendance::updateOrCreate([
                        'user_id' => $user->id,
                        'attendance_date' => $date,
                    ], [
                        'check_in' => $firstPunch->format('H:i:s'),
                        'check_out' => ($allLogs->count() > 1) ? $lastPunch->format('H:i:s') : null,
                        'total_hours' => $totalHours,
                        'status' => 'Present',
                    ]);
                }
                
                $syncedCount++;
            }
        }

        return redirect()->route('attendance.index')->with('success', "Attendance synced successfully. Processed {$syncedCount} records.");
    }
    public function receiveFromLocal(Request $request)
    {
        // Simple security check
        $token = $request->header('X-Sync-Token');
        if ($token !== env('SYNC_SECRET_TOKEN', 'my-secret-token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $attendanceLog = $request->input('records');
        if (empty($attendanceLog)) {
            return response()->json(['message' => 'No records provided'], 400);
        }

        $syncedCount = 0;
        $usersMap = User::all()->keyBy('id');

        foreach ($attendanceLog as $record) {
            $zktecoId = (int)$record['id']; 
            
            if (isset($usersMap[$zktecoId])) {
                $user = $usersMap[$zktecoId];
                $timestamp = Carbon::parse($record['timestamp']);
                $date = $timestamp->format('Y-m-d');
                
                $logId = $user->id . '_' . $timestamp->format('YmdHis');
                \App\Models\AttendanceLog::firstOrCreate(['log_id' => $logId], [
                    'user_id' => $user->id,
                    'log_time' => $timestamp,
                ]);

                $this->calculateDailySummary($user->id, $date);
                $syncedCount++;
            }
        }

        return response()->json(['message' => "Successfully processed {$syncedCount} records"]);
    }

    // --- ADMS (Cloud Server) Methods for Automatic Real-time Pushing ---

    public function admsHandshake(Request $request)
    {
        return response("OK\n");
    }

    public function admsGetRequest(Request $request)
    {
        return response("OK\n");
    }

    public function admsReceiveData(Request $request)
    {
        $table = $request->query('table');
        $rawData = $request->getContent();

        if ($table === 'ATTLOG') {
            $lines = explode("\n", trim($rawData));
            $usersMap = User::all()->keyBy('id');

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                $parts = preg_split('/\s+/', $line);
                if (count($parts) >= 2) {
                    $zkUserId = (int)$parts[0];
                    $timestampStr = $parts[1] . ' ' . ($parts[2] ?? '00:00:00');
                    
                    if (isset($usersMap[$zkUserId])) {
                        $user = $usersMap[$zkUserId];
                        $timestamp = Carbon::parse($timestampStr);
                        $date = $timestamp->format('Y-m-d');
                        
                        $logId = $user->id . '_' . $timestamp->format('YmdHis');
                        \App\Models\AttendanceLog::firstOrCreate(['log_id' => $logId], [
                            'user_id' => $user->id,
                            'log_time' => $timestamp,
                        ]);

                        $this->calculateDailySummary($user->id, $date);
                    }
                }
            }
        }
        return response("OK\n");
    }

    private function calculateDailySummary($userId, $date)
    {
        $allLogs = \App\Models\AttendanceLog::where('user_id', $userId)
            ->whereDate('log_time', $date)
            ->orderBy('log_time', 'asc')
            ->get();

        if ($allLogs->count() > 0) {
            $firstPunch = $allLogs->first()->log_time;
            $lastPunch = $allLogs->last()->log_time;
            
            $totalMinutes = 0;
            $logsArray = $allLogs->toArray();
            for ($i = 0; $i < count($logsArray) - 1; $i += 2) {
                $totalMinutes += Carbon::parse($logsArray[$i]['log_time'])->diffInMinutes(Carbon::parse($logsArray[$i+1]['log_time']));
            }
            $totalHours = round($totalMinutes / 60, 2);

            Attendance::updateOrCreate(['user_id' => $userId, 'attendance_date' => $date], [
                'check_in' => $firstPunch->format('H:i:s'),
                'check_out' => ($allLogs->count() > 1) ? $lastPunch->format('H:i:s') : null,
                'total_hours' => $totalHours,
                'status' => 'Present',
            ]);
        }
    }
}
