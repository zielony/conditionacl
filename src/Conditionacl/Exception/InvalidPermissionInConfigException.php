<?php

declare(strict_types=1);

namespace Conditionacl\Exception;

class InvalidPermissionInConfigException extends \InvalidArgumentException implements ConditionaclException
{
    public static function fromPermission($permission, int $code = 0, \Throwable $previous = null): self
    {
        return new static('The configuration contains invalid permission of type '.\get_class($permission).'!', $code, $previous);
    }
}
