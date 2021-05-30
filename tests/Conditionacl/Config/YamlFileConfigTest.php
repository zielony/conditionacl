<?php

declare(strict_types=1);

namespace Conditionacl\Config;

use Conditionacl\Permission;
use Conditionacl\PermissionsList;
use Conditionacl\Test\FunctionalTest;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class YamlFileConfigTest extends TestCase
{
    public function testGetRoles(): void
    {
        $config = new YamlFileConfig(FunctionalTest::CONFIG_FILE_PATH);

        static::assertEquals(
            [
                'project-reader' => new PermissionsList(
                    [
                        new Permission('read', 'project'),
                    ]
                ),
                'project-writer' => new PermissionsList(
                    [
                        new Permission('write', 'project'),
                        new Permission('update', 'project'),
                        new Permission('delete', 'project'),
                    ]
                ),
                'project-admin' => new PermissionsList(
                    [
                        new Permission('read', 'project'),
                        new Permission('write', 'project'),
                        new Permission('update', 'project'),
                        new Permission('delete', 'project'),
                    ]
                ),
                'user-admin' => new PermissionsList(
                    [
                        new Permission('read', 'user'),
                        new Permission('write', 'user'),
                        new Permission('update', 'user'),
                        new Permission('delete', 'user'),
                    ]
                ),
            ],
            $config->getRoles()
        );
    }
}
