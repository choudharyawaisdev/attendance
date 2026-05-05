<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceSyncController extends Controller
{
    /**
     * Receive attendance records from the local sync agent
     */
    public function receive(Request $request)
    {
        // Yahan par hum ek basic security token check laga sakte hain
        $serverToken = env('SYNC_SECRET_TOKEN', 'my-super-secret-token');
        $clientToken = $request->header('X-Sync-Token');

        if ($serverToken !== $clientToken) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $records = $request->input('records', []);
        $syncedCount = 0;

        foreach ($records as $record) {
            $zktecoId = $record['id']; // Device User ID
            $timestampStr = $record['timestamp'];
            
            $user = User::where('id', $zktecoId)->first();
            
            if ($user) {
                $timestamp = Carbon::parse($timestampStr);
                $date = $timestamp->format('Y-m-d');
                $time = $timestamp->format('H:i:s');
                
                $attendance = Attendance::firstOrNew([
                    'user_id' => $user->id,
                    'attendance_date' => $date,
                ]);

                if (!$attendance->exists || empty($attendance->check_in)) {
                    $attendance->check_in = $time;
                    $attendance->status = 'Present';
                    $attendance->notes = 'Synced from Local Agent';
                } else {
                    if (Carbon::parse($time)->gt(Carbon::parse($attendance->check_in))) {
                        $attendance->check_out = $time;
                    }
                }
                
                $attendance->save();
                $syncedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully synced {$syncedCount} records."
        ]);
    }
}
