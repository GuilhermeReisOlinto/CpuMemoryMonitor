<?php

use CpuMemoryMonitor\SystemMonitor;

require __DIR__ . "/../vendor/autoload.php";

$monitor = new SystemMonitor();

echo $monitor->generateReport();
// echo $monitor->getCpuUsage();
// echo $monitor->getMemoryUsage();
// echo $monitor->getTopCpuProcesses();
// echo $monitor->getTopMemoryProcesses();
echo $monitor->generateReport();
