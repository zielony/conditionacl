<?php

declare(strict_types=1);

namespace Conditionacl\Config;

use Conditionacl\PermissionsList;

abstract class AclConfig
{
    /** @var PermissionsList[] */
    protected array $roles;

    /**
     * @return PermissionsList[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    abstract public function getConfigType(): string;
}
