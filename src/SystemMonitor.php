<?php

namespace CpuMemoryMonitor;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class SystemMonitor
{
    private string $logFile;
    private float $cpuThreshold;
    private float $memoryThreshold;
    private string $emailTo;
    private string $emailFrom;
    private string $smtpHost;
    private string $smtpUser;
    private string $smtpPass;
    private string $smtpPort;

    public function __construct(float $cpuThreshold = 80.0, float $memoryThreshold = 80.0, string $emailTo)
    {
        $this->logFile = __DIR__ . "/../logs/system_usage.log";
        $this->cpuThreshold = $cpuThreshold;
        $this->memoryThreshold = $memoryThreshold;
        $this->emailTo = $emailTo;

        $this->emailFrom = "example@gmail.com";
        $this->smtpHost = "smtp.gmail.com";
        $this->smtpUser = "example@gmail.com";
        $this->smtpPass = "examplesenha";
        $this->smtpPort = 587;
    }

    /**
     * Obtém o uso de CPU em percentual
     */
    public function getCpuUsage(): string
    {
        $command = "top -bn1 | grep 'Cpu(s)' | awk '{print $2 + $4}'";
        $output = shell_exec($command);

        return trim($output) . "% de uso da cpu \n";
    }

    /**
     * Obtém o uso de memória RAM em MB
     */

    public function getMemoryUsage(): string
    {
        $command = "free -m | awk 'NR==2{printf \"%s/%sMB (%.2f%%)\", $3, $2, $3*100/$2}'";
        $output = shell_exec($command);

        return "Uso de memória: " . trim($output) . "\n";
    }

    /**
     * Lista os 5 processos que mais consomem CPU
     */

    public function getTopCpuProcesses(): string
    {
        $command = "ps aux --sort=-%cpu | head -n 6";
        $output = shell_exec($command);

        return "Processos com maior uso de CPU:\n" . $output . "\n";
    }

    /**
     * Lista os 5 processos que mais consomem memória
     */
    public function getTopMemoryProcesses(): string
    {
        $command = "ps aux --sort=-%mem | head -n 6";
        $output = shell_exec($command);

        return "Processos com maior uso de Memória:\n" . $output . "\n";
    }

    /**
     * Gera um relatório de uso de CPU e memória
     */

    public function generateReport(): string
    {
        $report = "===== Relatório de Consumo ====\n" .
            "Data: " . date('y-m-d H:i:s') . "\n" .
            $this->getCpuUsage() . "\n" .
            $this->getMemoryUsage() . "\n" .
            $this->getTopCpuProcesses() . "\n\n" .
            $this->getTopMemoryProcesses();

        $this->logToFile($report);

        return $report;
    }

    private function logToFile(string $data): void
    {
        file_put_contents($this->logFile, $data, FILE_APPEND);
    }

    public function checkAlerts(): string
    {
        $alerts = [];
        $cpuUsage = $this->getCpuUsage();
        $memoryUsage = $this->getMemoryUsage();

        if ($cpuUsage > $this->cpuThreshold) {
            $alerts[] = "ALERTA: Uso de CPU muito alto! {$cpuUsage}%";
        }

        if ($memoryUsage > $this->memoryThreshold) {
            $alerts[] = "ALERTA: Uso de memória muito alto! {$memoryUsage}%";
        }

        if (!empty($alerts)) {
            $alertsMessage = implode("\n", $alerts);
            $this->logToFile($alertsMessage);
            $this->sendEmail($alertsMessage);
            return $alertsMessage;
        }

        return "Tudo normal. CPU: {$cpuUsage}%, Memória: {$memoryUsage}%";
    }

    private function sendEmail(string $message): void
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $this->smtpHost;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->smtpUser;
            $mail->Password   = $this->smtpPass;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $this->smtpPort;

            $mail->setFrom($this->emailFrom, 'Monitor de Sistemas');
            $mail->addAddress($this->emailTo);

            $mail->Subject = "Alerta de uso de CPU/Memória!";
            $mail->Body = $message;

            $mail->send();
        } catch (Exception $e) {
            $this->logToFile("Erro ao enviar email: " . $mail->ErrorInfo);
        }
    }
}
