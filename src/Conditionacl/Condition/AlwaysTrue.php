<?php

declare(strict_types=1);

namespace Conditionacl\Condition;

use Conditionacl\Condition;

final class AlwaysTrue implements Condition
{
    public function isMet(): bool
    {
        return true;
    }
}
