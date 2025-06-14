<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class LogService
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function log($message)
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'INCONNU';
        $deviceType = $this->getDeviceType($userAgent);

        $this->logger->info($message, array_merge([
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'INCONNU',
            'datetime' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            'type_device' => $deviceType,
        ]));
    }

    private function getDeviceType(string $userAgent): string
    {
        $userAgent = strtolower($userAgent);

        if (preg_match('/mobile|android|iphone|ipod|blackberry|windows phone/', $userAgent)) {
            return 'mobile';
        }

        if (preg_match('/tablet|ipad/', $userAgent)) {
            return 'tablette';
        }

        if (preg_match('/dart|flutter/', $userAgent)) {
            return 'aplication mobile';
        }

        return 'ordinateur';
    }
}