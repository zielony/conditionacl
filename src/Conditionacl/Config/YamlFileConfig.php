<?php

declare(strict_types=1);

namespace Conditionacl\Config;

use Conditionacl\Exception\NoRolesInConfigException;
use Conditionacl\Permission;
use Conditionacl\PermissionsList;
use Symfony\Component\Yaml\Yaml;

final class YamlFileConfig extends AclConfig
{
    public function __construct(string $path)
    {
        $contents = Yaml::parseFile($path);
        if (!\array_key_exists('roles', $contents) || empty($contents['roles'])) {
            throw NoRolesInConfigException::fromConfigType($this->getConfigType());
        }

        foreach ($contents['roles'] as $roleName => $permissionStatements) {
            $permissions = [];

            foreach ($permissionStatements as $permissionStatement) {
                list($operation, $subject) = explode(':', $permissionStatement);
                $permissions[] = new Permission($operation, $subject);
            }

            $this->roles[$roleName] = new PermissionsList($permissions);
        }
    }

    public function getConfigType(): string
    {
        return 'yaml';
    }
}
