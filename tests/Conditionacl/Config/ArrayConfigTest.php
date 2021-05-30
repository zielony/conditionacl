<?php

declare(strict_types=1);

namespace Conditionacl\Config;

use Conditionacl\Permission;
use Conditionacl\PermissionsList;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class ArrayConfigTest extends TestCase
{
    public function testGetRoles(): void
    {
        $config = new ArrayConfig(
            [
                'roles' => [
                    'write-project' => 'write:project',
                    'read-project' => new Permission('read', 'project'),
                ],
            ]
        );

        static::assertEquals(
            [
                'write-project' => new PermissionsList([new Permission('write', 'project')]),
                'read-project' => new PermissionsList([new Permission('read', 'project')]),
            ],
            $config->getRoles()
        );
    }
}
