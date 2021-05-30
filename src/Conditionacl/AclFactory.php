<?php

declare(strict_types=1);

namespace Conditionacl;

use Conditionacl\Config\AclConfig;
use Conditionacl\Exception\UndefinedRoleException;

final class AclFactory
{
    /** @var PermissionsList[] */
    private array $roles;

    public function __construct(AclConfig $config)
    {
        foreach ($config->getRoles() as $roleName => $list) {
            $this->roles[$roleName] = $list;
        }
    }

    public function fromRole(string $roleName): Acl
    {
        if (!\array_key_exists($roleName, $this->roles)) {
            throw UndefinedRoleException::fromRoleName($roleName);
        }

        return new Acl($this->roles[$roleName]);
    }
}
