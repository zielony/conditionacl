<?php

declare(strict_types=1);

namespace Conditionacl\Config;

use Conditionacl\Exception\InvalidPermissionInConfigException;
use Conditionacl\Exception\NoRolesInConfigException;
use Conditionacl\Permission;
use Conditionacl\PermissionsList;

class ArrayConfig extends AclConfig
{
    public function __construct(array $options)
    {
        if (empty($options['roles'])) {
            throw NoRolesInConfigException::fromConfigType($this->getConfigType());
        }

        foreach ($options['roles'] as $name => $value) {
            if (!\is_array($value)) {
                $value = [$value];
            }

            $permissions = [];

            foreach ($value as $permission) {
                if ($permission instanceof Permission) {
                    $permissions[] = $permission;
                } elseif (\is_string($permission)) {
                    list($operation, $subject) = explode(':', $permission);
                    $permissions[] = new Permission($operation, $subject);
                } else {
                    throw InvalidPermissionInConfigException::fromPermission($permission);
                }
            }

            $this->roles[$name] = new PermissionsList($permissions);
        }
    }

    public function getConfigType(): string
    {
        return 'array';
    }
}
