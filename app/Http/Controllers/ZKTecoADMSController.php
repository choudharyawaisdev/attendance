<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class ZKTecoADMSController extends Controller
{
    /**
     * Handle initial connection (Handshake) from the device
     * Device calls: GET /iclock/cdata?SN=...&options=...
     */
    public function handshake(Request $request)
    {
        $sn = $request->query('SN');
        Log::info("ZKTeco ADMS Handshake Request from Device SN: {$sn}");
        Log::info("Handshake parameters: ", $request->all());

        // We must return a specific response to tell the device the connection is successful
        return response("OK\n");
    }

    /**
     * Handle incoming data (Attendance logs, User data, etc) from the device
     * Device calls: POST /iclock/cdata?SN=...&table=ATTLOG
     */
    public function receiveData(Request $request)
    {
        $sn = $request->query('SN');
        $table = $request->query('table');
        
        Log::info("ZKTeco ADMS Data Request from Device SN: {$sn}, Table: {$table}");
        
        // The raw body contains the data logs sent by the machine
        $rawData = $request->getContent();
        Log::info("Raw Data Received:\n" . $rawData);

        if ($table === 'ATTLOG') {
            $this->processAttendanceData($rawData);
        }

        // Must return OK and the number of records processed, or just OK
        return response("OK\n");
    }

    /**
     * Handle commands request from device.
     * The device periodically asks if the server has any commands (like adding a user) for it.
     * Device calls: GET /iclock/getrequest?SN=...
     */
    public function getRequest(Request $request)
    {
        $sn = $request->query('SN');
        // We don't have commands to send right now, so we return OK
        return response("OK\n");
    }

    /**
     * Process raw attendance data sent by ADMS
     * Format usually looks like:
     * 1 2023-10-25 09:00:00 1 1 0 0
     * UserId Timestamp Status ...
     */
    private function processAttendanceData($rawData)
    {
        $lines = explode("\n", trim($rawData));
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // ADMS log format is tab or space separated
            // Example: 1<tab>2026-05-05 14:00:00<tab>1<tab>1
            $parts = preg_split('/\s+/', $line);
            
            if (count($parts) >= 2) {
                $zktecoUserId = $parts[0];
                $timestampStr = $parts[1] . ' ' . (isset($parts[2]) ? $parts[2] : '00:00:00');
                
                try {
                    $timestamp = Carbon::parse($timestampStr);
                    $date = $timestamp->format('Y-m-d');
                    $time = $timestamp->format('H:i:s');

                    $user = User::where('id', $zktecoUserId)->first();

                    if ($user) {
                        $attendance = Attendance::firstOrNew([
                            'user_id' => $user->id,
                            'attendance_date' => $date,
                        ]);

                        if (!$attendance->exists || empty($attendance->check_in)) {
                            $attendance->check_in = $time;
                            $attendance->status = 'Present';
                            $attendance->notes = 'ADMS Push';
                        } else {
                            if (Carbon::parse($time)->gt(Carbon::parse($attendance->check_in))) {
                                $attendance->check_out = $time;
                                $attendance->notes = 'ADMS Push (Check-out)';
                            }
                        }
                        
                        $attendance->save();
                    } else {
                        Log::warning("ZKTeco ADMS: User not found for ID {$zktecoUserId}");
                    }
                } catch (\Exception $e) {
                    Log::error("ZKTeco ADMS Parse Error for line: {$line}. Error: " . $e->getMessage());
                }
            }
        }
    }
}
