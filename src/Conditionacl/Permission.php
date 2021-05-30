<?php

declare(strict_types=1);

namespace Conditionacl;

class Permission
{
    protected string $operation;
    protected string $subject;

    public function __construct(string $operation, string $subject)
    {
        $this->operation = $operation;
        $this->subject = $subject;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }
}
