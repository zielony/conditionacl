<?php /** @noinspection PhpParamsInspection */

declare(strict_types=1);

namespace Conditionacl;

use Conditionacl\Config\AclConfig;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @internal
 * @coversNothing
 */
class AclFactoryTest extends TestCase
{
    use ProphecyTrait;

    private AclFactory $aclFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $config = $this->prophesize(AclConfig::class);
        $config->getRoles()->willReturn([
            'project-reader' => new PermissionsList([new Permission('read', 'project')]),
            'project-writer' => new PermissionsList(
                [
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
        ]);

        $this->aclFactory = new AclFactory($config->reveal());
    }

    public function testFromRole(): void
    {
        $acl1 = $this->aclFactory->fromRole('project-reader');
        $acl2 = $this->aclFactory->fromRole('project-writer');
        $acl3 = $this->aclFactory->fromRole('user-admin');

        $this->assertInstanceOf(Acl::class, $acl1);
        $this->assertInstanceOf(Acl::class, $acl2);
        $this->assertInstanceOf(Acl::class, $acl3);
    }
}
