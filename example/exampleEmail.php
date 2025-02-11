<?php

require __DIR__ . '/../vendor/autoload.php';

use CpuMemoryMonitor\SystemMonitor;

$monitor = new SystemMonitor(70, 70, "example@gmail.com");
echo $monitor->checkAlerts();
