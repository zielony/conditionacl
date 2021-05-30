<?php

declare(strict_types=1);

namespace Conditionacl\Exception;

class UndefinedRoleException extends \UnexpectedValueException implements ConditionaclException
{
    public static function fromRoleName(string $roleName, int $code = 0, \Throwable $previous = null): self
    {
        return new static('Role '.$roleName.' has not been defined in the config!', $code, $previous);
    }
}
