<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\Workflow\Exception\LogicException;

class TopicStateException extends \Exception
{
    private LogicException $previousException;

    public function __construct(string $message, int $code, LogicException $previousException)
    {
        parent::__construct($message, $code, $previousException);
        $this->previousException = $previousException;
    }

    public function getPreviousException(): LogicException
    {
        return $this->previousException;
    }
}
