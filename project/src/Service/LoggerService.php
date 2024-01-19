<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

readonly class LoggerService
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    /**
     * @param array<string> $subjects
     */
    public function log(
        string $level,
        string $messageFormat,
        ?array $subjects = null,
        string|\Throwable $exception = ''
    ): void {

        $exceptionMessage = $exception instanceof \Throwable
            ? sprintf(' Exception: %s', $exception->getMessage())
            : '';
        $implodedSubjects = $subjects ? implode(', ', $subjects) : '';

        $message = sprintf('%s%s', sprintf($messageFormat, $implodedSubjects), $exceptionMessage);

        $this->logger->log($level, $message);
    }
}
