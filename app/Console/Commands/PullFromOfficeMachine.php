<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Jmrashed\Zkteco\Lib\ZKTeco;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use Carbon\Carbon;

class PullFromOfficeMachine extends Command
{
    protected $signature = 'zkteco:pull-from-office';
    protected $description = 'Connect to Office Static IP and pull attendance data to Cloud Server';

    public function handle()
    {
        $this->info('Connecting to Office Machine via Public IP...');
        
        $ip = env('ZKTECO_IP', 'your-office-public-ip');
        $port = env('ZKTECO_PORT', 4370);

        $zk = new ZKTeco($ip, $port);
        
        if (!$zk->connect()) {
            $this->error("Could not connect to Office Machine at {$ip}. Check Port Forwarding!");
            return;
        }

        $this->info('Connected! Fetching logs...');
        $logs = $zk->getAttendance();
        $zk->disconnect();

        if (empty($logs)) {
            $this->warn('No logs found.');
            return;
        }

        $this->info('Processing ' . count($logs) . ' records...');
        $usersMap = User::all()->keyBy('id');
        $syncedCount = 0;

        foreach ($logs as $record) {
            $zkUserId = (int)$record['id'];
            if (isset($usersMap[$zkUserId])) {
                $user = $usersMap[$zkUserId];
                $timestamp = Carbon::parse($record['timestamp']);
                $date = $timestamp->format('Y-m-d');
                
                $logId = $user->id . '_' . $timestamp->format('YmdHis');
                AttendanceLog::firstOrCreate(['log_id' => $logId], [
                    'user_id' => $user->id,
                    'log_time' => $timestamp,
                ]);

                $this->updateSummary($user->id, $date);
                $syncedCount++;
            }
        }

        $this->info("Successfully synced {$syncedCount} records.");
    }

    private function updateSummary($userId, $date)
    {
        $allLogs = AttendanceLog::where('user_id', $userId)
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
