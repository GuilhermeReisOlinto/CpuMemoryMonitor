<?php

use CpuMemoryMonitor\SystemMonitor;
use PHPUnit\Framework\TestCase;

class SystemMonitorTest extends TestCase
{
    private $monitor;
    private $logFile;


    protected function setUp(): void
    {
        $this->monitor = new SystemMonitor();
        $this->logFile = __DIR__ . "/../logs/system_usage.log";
    }

    public function testCpuUsageReturnsString()
    {
        $this->assertIsString($this->monitor->getCpuUsage());
    }

    public function testMemoryUsageReturnsString()
    {
        $this->assertIsString($this->monitor->getMemoryUsage());
    }

    public function testLogFileIsWritten()
    {
        $this->monitor->generateReport();

        $this->assertFileExists($this->logFile);
        $this->assertGreaterThan(0, filesize($this->logFile), "O log está vazio!");
    }
}
