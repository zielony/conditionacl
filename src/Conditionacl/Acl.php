<?php

declare(strict_types=1);

namespace Conditionacl;

use Conditionacl\Condition\AlwaysTrue;

final class Acl
{
    private PermissionsList $permissions;

    public function __construct(PermissionsList $permissions)
    {
        $this->permissions = $permissions;
    }

    public function hasPermissionTo(string $operation, string $subject, Condition $condition = null): bool
    {
        if (null === $condition) {
            $condition = new AlwaysTrue();
        }

        return $condition->isMet() && $this->permissions->has($operation, $subject);
    }

    public function getPermissions(): PermissionsList
    {
        return $this->permissions;
    }

    public function add(self $anotherAcl): self
    {
        return new self($this->permissions->merge($anotherAcl->getPermissions()));
    }

    public function subtract(self $anotherAcl): self
    {
        return new self($this->permissions->diff($anotherAcl->getPermissions()));
    }
}
