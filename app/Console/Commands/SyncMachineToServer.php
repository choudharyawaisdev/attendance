<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Jmrashed\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Http;

class SyncMachineToServer extends Command
{
    protected $signature = 'zkteco:sync-to-server';
    protected $description = 'Fetch data from local machine and push to Cloud Server';

    public function handle()
    {
        $this->info('Connecting to local ZKTeco machine...');
        
        $ip = env('ZKTECO_IP', '192.168.1.201');
        $port = env('ZKTECO_PORT', 4370);
        $serverUrl = env('LIVE_SERVER_URL', 'http://your-domain.com');
        $token = env('SYNC_SECRET_TOKEN', 'my-secret-token');

        $zk = new ZKTeco($ip, $port);
        
        if (!$zk->connect()) {
            $this->error("Could not connect to machine at {$ip}");
            return;
        }

        $this->info('Connected! Fetching logs...');
        $logs = $zk->getAttendance();
        $zk->disconnect();

        if (empty($logs)) {
            $this->warn('No logs found on device.');
            return;
        }

        $this->info('Found ' . count($logs) . ' records. Pushing to server: ' . $serverUrl);

        $response = Http::withHeaders([
            'X-Sync-Token' => $token,
        ])->post($serverUrl . '/api/sync-from-local', [
            'records' => $logs
        ]);

        if ($response->successful()) {
            $this->info('Success: ' . $response->json('message'));
        } else {
            $this->error('Failed: ' . $response->body());
        }
    }
}
