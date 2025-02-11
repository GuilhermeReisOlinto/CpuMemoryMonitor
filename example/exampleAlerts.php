<?php

use CpuMemoryMonitor\SystemMonitor;

require __DIR__ . "/../vendor/autoload.php";

$monitor = new SystemMonitor(80, 80);

echo $monitor->checkAlerts();
