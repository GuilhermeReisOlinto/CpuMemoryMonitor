<?php

require __DIR__ . '/../vendor/autoload.php';

use CpuMemoryMonitor\SystemMonitor;

$monitor = new SystemMonitor(70, 70, "reis47468@gmail.com");
echo $monitor->checkAlerts();
