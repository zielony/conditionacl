<?php /** @noinspection PhpParamsInspection */

declare(strict_types=1);

namespace Conditionacl\Test;

use Conditionacl\AclFactory;
use Conditionacl\Condition;
use Conditionacl\Config\ArrayConfig;
use Conditionacl\Config\YamlFileConfig;
use Conditionacl\Exception\InvalidPermissionInConfigException;
use Conditionacl\Exception\NoRolesInConfigException;
use Conditionacl\Exception\UndefinedRoleException;
use Conditionacl\Permission;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Yaml\Yaml;

/**
 * @internal
 * @coversNothing
 */
class FunctionalTest extends TestCase
{
    use ProphecyTrait;

    public const CONFIG_FILE_PATH = 'tests/resources/config.yml';

    private AclFactory $aclFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $config = new YamlFileConfig(self::CONFIG_FILE_PATH);
        $this->aclFactory = new AclFactory($config);
    }

    public function testCanReadPermissionsFromYaml(): void
    {
        $data = Yaml::parseFile(self::CONFIG_FILE_PATH);
        foreach ($data['roles'] as $roleName => $permissionStatements) {
            $acl = $this->aclFactory->fromRole($roleName);

            foreach ($permissionStatements as $permissionStatement) {
                list($operation, $subject) = explode(':', $permissionStatement);
                static::assertTrue($acl->hasPermissionTo($operation, $subject));
            }

            static::assertFalse($acl->hasPermissionTo('non-existent-operation', 'non-existent-subject'));
        }
    }

    public function testCanReadPermissionsFromArray(): void
    {
        $roles = [
            'project-reader' => 'read:project',
            'project-writer' => [
                'write:project',
                'update:project',
                'delete:project',
            ],
            'user-admin' => [
                new Permission('read', 'user'),
                new Permission('write', 'user'),
                new Permission('update', 'user'),
                new Permission('delete', 'user'),
            ],
        ];

        $aclFactory = new AclFactory(new ArrayConfig(['roles' => $roles]));

        foreach ($roles as $roleName => $permissionStatements) {
            $acl = $aclFactory->fromRole($roleName);

            if (!\is_array($permissionStatements)) {
                $permissionStatements = [$permissionStatements];
            }

            foreach ($permissionStatements as $permissionStatement) {
                if ($permissionStatement instanceof Permission) {
                    list($operation, $subject) = [
                        $permissionStatement->getOperation(),
                        $permissionStatement->getSubject(),
                    ];
                } else {
                    list($operation, $subject) = explode(':', $permissionStatement);
                }

                static::assertTrue($acl->hasPermissionTo($operation, $subject));
            }

            static::assertFalse($acl->hasPermissionTo('non-existent-operation', 'non-existent-subject'));
        }
    }

    public function testCanTakeConditions(): void
    {
        $conditionNotMet = $this->prophesize(Condition::class);
        $conditionNotMet->isMet()->shouldBeCalled()->willReturn(false);

        $conditionMet = $this->prophesize(Condition::class);
        $conditionMet->isMet()->shouldBeCalled()->willReturn(true);

        $acl = $this->aclFactory->fromRole('project-reader');
        static::assertTrue($acl->hasPermissionTo('read', 'project', $conditionMet->reveal()));
        static::assertFalse($acl->hasPermissionTo('read', 'project', $conditionNotMet->reveal()));
    }

    public function testCanMergeAcls(): void
    {
        $aclProjectReader = $this->aclFactory->fromRole('project-reader');
        $aclProjectWriter = $this->aclFactory->fromRole('project-writer');
        $aclProjectAdmin = $aclProjectReader->add($aclProjectWriter);

        static::assertTrue($aclProjectAdmin->hasPermissionTo('read', 'project'));
        static::assertTrue($aclProjectAdmin->hasPermissionTo('write', 'project'));
        static::assertTrue($aclProjectAdmin->hasPermissionTo('update', 'project'));
        static::assertTrue($aclProjectAdmin->hasPermissionTo('delete', 'project'));
        static::assertFalse($aclProjectAdmin->hasPermissionTo('purge', 'project'));
    }

    public function testCanSubtractAcls(): void
    {
        $aclProjectAdmin = $this->aclFactory->fromRole('project-admin');
        $aclProjectWriter = $this->aclFactory->fromRole('project-writer');
        $aclProjectReader = $aclProjectAdmin->subtract($aclProjectWriter);

        static::assertTrue($aclProjectReader->hasPermissionTo('read', 'project'));
        static::assertFalse($aclProjectReader->hasPermissionTo('write', 'project'));
        static::assertFalse($aclProjectReader->hasPermissionTo('update', 'project'));
        static::assertFalse($aclProjectReader->hasPermissionTo('delete', 'project'));
        static::assertFalse($aclProjectReader->hasPermissionTo('purge', 'project'));
    }

    public function testThrowsExceptionOnUndefinedRole(): void
    {
        $this->expectException(UndefinedRoleException::class);
        $this->aclFactory->fromRole('non-existent');
    }

    public function testThrowsExceptionOnInvalidPermissionInConfig(): void
    {
        $this->expectException(InvalidPermissionInConfigException::class);

        $roles = [
            'test' => new \stdClass(),
        ];

        new AclFactory(new ArrayConfig(['roles' => $roles]));
    }

    public function testThrowsExceptionWhenMissesRolesInConfig(): void
    {
        $this->expectException(NoRolesInConfigException::class);

        new AclFactory(new ArrayConfig([]));
    }
}
