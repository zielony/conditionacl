<?php

declare(strict_types=1);

namespace Conditionacl\Exception;

class NoRolesInConfigException extends \RuntimeException implements ConditionaclException
{
    public static function fromConfigType(string $configType, int $code = 0, \Throwable $previous = null): self
    {
        return new static('The configuration '.$configType.' passed  does not contain a "roles" node.', $code, $previous);
    }
}
