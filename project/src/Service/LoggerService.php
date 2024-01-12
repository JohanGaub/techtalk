<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;

readonly class LoggerService
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function log(
        string            $level,
        string            $messageFormat,
        array             $subjects = null,
        \Throwable|string $exception = ''
    ): void {
        $exceptionMessage = $exception instanceof \Throwable
            ? sprintf(' Exception: %s', $exception->getMessage())
            : '';
        $implodedSubjects = $subjects ? implode(', ', $subjects) : '';

        $message = sprintf($messageFormat, $implodedSubjects, $exceptionMessage);

        $this->logger->log($level, $message);
    }
}
