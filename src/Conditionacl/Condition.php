<?php

declare(strict_types=1);

namespace Conditionacl;

interface Condition
{
    public function isMet(): bool;
}
