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
        $ip = env('ZKTECO_IP', '192.168.1.201');
        $port = env('ZKTECO_PORT', 4370);
        
        $zk = new ZKTeco($ip, $port);
        $connected = $zk->connect();

        if (!$connected) {
            return redirect()->route('attendance.index')->with('error', 'Unable to connect to ZKTeco device.');
        }

        $attendanceLog = $zk->getAttendance();
        $zk->disconnect();

        if (empty($attendanceLog)) {
            return redirect()->route('attendance.index')->with('success', 'Connected to device, but no attendance records found.');
        }

        $syncedCount = 0;

        foreach ($attendanceLog as $record) {
            $zktecoId = $record['id']; // ID string from device
            
            // User ki ID se check karein (ZKTeco device ID == User ID)
            $user = User::where('id', $zktecoId)->first();
            
            if ($user) {
                $timestamp = Carbon::parse($record['timestamp']);
                $date = $timestamp->format('Y-m-d');
                $time = $timestamp->format('H:i:s');
                
                // Find or create attendance for this user on this date
                $attendance = Attendance::firstOrNew([
                    'user_id' => $user->id,
                    'attendance_date' => $date,
                ]);

                // Check-in (Type 0 or State 1 typically means check in, but let's just use first punch as IN, last as OUT)
                // If attendance doesn't exist or check_in is empty, it's check in
                if (!$attendance->exists || empty($attendance->check_in)) {
                    $attendance->check_in = $time;
                    $attendance->status = 'Present';
                } else {
                    // Update check_out if the time is later than check_in
                    if (Carbon::parse($time)->gt(Carbon::parse($attendance->check_in))) {
                        $attendance->check_out = $time;
                    }
                }
                
                $attendance->save();
                $syncedCount++;
            }
        }

        return redirect()->route('attendance.index')->with('success', "Attendance synced successfully. Processed {$syncedCount} records.");
    }
}
