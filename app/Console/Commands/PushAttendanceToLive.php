<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Jmrashed\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushAttendanceToLive extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'zkteco:push-to-live {--clear : Clear attendance from device after syncing}';

    /**
     * The console command description.
     */
    protected $description = 'Fetch attendance from local ZKTeco device and push to Live Server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sync process...');

        $ip = env('ZKTECO_IP', '192.168.1.201');
        $port = env('ZKTECO_PORT', 4370);
        $liveServerUrl = env('LIVE_SERVER_URL', 'http://127.0.0.1:8000'); // Change this in .env to your live domain
        $syncToken = env('SYNC_SECRET_TOKEN', 'my-super-secret-token');

        $zk = new ZKTeco($ip, $port);
        
        if (!$zk->connect()) {
            $this->error("Failed to connect to local ZKTeco device at {$ip}:{$port}");
            return Command::FAILURE;
        }

        $this->info('Connected to device. Fetching attendance logs...');
        $attendanceLog = $zk->getAttendance();

        if (empty($attendanceLog)) {
            $this->info('No attendance records found on the device.');
            $zk->disconnect();
            return Command::SUCCESS;
        }

        $this->info('Found ' . count($attendanceLog) . ' records. Pushing to Live Server...');

        // Send POST request to Live Server
        $response = Http::withHeaders([
            'X-Sync-Token' => $syncToken,
            'Accept' => 'application/json'
        ])->post("{$liveServerUrl}/api/attendance/sync", [
            'records' => $attendanceLog
        ]);

        if ($response->successful()) {
            $this->info('Successfully synced with Live Server!');
            $this->line($response->json('message'));

            if ($this->option('clear')) {
                $zk->clearAttendance();
                $this->info('Cleared attendance logs from the local device.');
            }
        } else {
            $this->error('Failed to sync with Live Server.');
            $this->error('Status Code: ' . $response->status());
            $this->error('Response: ' . $response->body());
        }

        $zk->disconnect();
        return Command::SUCCESS;
    }
}
