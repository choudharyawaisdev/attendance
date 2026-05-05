<?php
require 'vendor/autoload.php';
use Jmrashed\Zkteco\Lib\ZKTeco;
$zk = new ZKTeco('192.168.1.201', 4370);
if ($zk->connect()) {
    echo "Connected successfully!\n";
    $users = $zk->getUser();
    echo "Users in machine:\n";
    print_r($users);
    $logs = $zk->getAttendance(5);
    echo "\nLatest 5 logs in machine:\n";
    print_r($logs);
    $zk->disconnect();
} else {
    echo "Failed to connect.\n";
}
