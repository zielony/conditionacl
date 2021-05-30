<?php /** @noinspection PhpParamsInspection */

declare(strict_types=1);

namespace Conditionacl;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @internal
 * @coversNothing
 */
class AclTest extends TestCase
{
    use ProphecyTrait;

    private Acl $acl1;
    private Acl $acl2;
    private Acl $acl3;
    private Acl $acl4;

    protected function setUp(): void
    {
        parent::setUp();

        $this->acl1 = new Acl(new PermissionsList(
            [
                new Permission('read', 'project'),
            ]
        ));

        $this->acl2 = new Acl(new PermissionsList(
            [
                new Permission('read', 'project'),
                new Permission('write', 'project'),
            ]
        ));

        $this->acl3 = new Acl(new PermissionsList(
            [
                new Permission('read', 'project'),
                new Permission('write', 'project'),
                new Permission('delete', 'project'),
            ]
        ));

        $this->acl4 = new Acl(new PermissionsList(
            [
                new Permission('write', 'project'),
                new Permission('delete', 'project'),
                new Permission('update', 'project'),
            ]
        ));
    }

    public function testAdd(): void
    {
        $result1 = $this->acl1->add($this->acl2);
        $result2 = $this->acl2->add($this->acl3);
        $result3 = $this->acl1->add($this->acl3);
        $result4 = $this->acl1->add($this->acl4);

        $this->assertCount(2, $result1->getPermissions());
        $this->assertCount(3, $result2->getPermissions());
        $this->assertCount(3, $result3->getPermissions());
        $this->assertCount(4, $result4->getPermissions());
    }

    public function testSubtract(): void
    {
        $result1 = $this->acl1->subtract($this->acl2);
        $result2 = $this->acl2->subtract($this->acl1);
        $result3 = $this->acl3->subtract($this->acl1);
        $result4 = $this->acl4->subtract($this->acl1);
        $result5 = $this->acl4->subtract($this->acl3);
        $result6 = $this->acl3->subtract($this->acl2);

        $this->assertCount(0, $result1->getPermissions());
        $this->assertCount(1, $result2->getPermissions());
        $this->assertCount(2, $result3->getPermissions());
        $this->assertCount(3, $result4->getPermissions());
        $this->assertCount(1, $result5->getPermissions());
        $this->assertCount(1, $result6->getPermissions());
    }

    public function testHasPermissionTo(): void
    {
        $conditionMet = $this->prophesize(Condition::class);
        $conditionMet->isMet()->willReturn(true);

        $conditionNotMet = $this->prophesize(Condition::class);
        $conditionNotMet->isMet()->willReturn(false);

        $this->assertTrue($this->acl1->hasPermissionTo('read', 'project'));
        $this->assertTrue($this->acl2->hasPermissionTo('write', 'project'));
        $this->assertTrue($this->acl3->hasPermissionTo('delete', 'project'));
        $this->assertTrue($this->acl4->hasPermissionTo('update', 'project'));

        $this->assertTrue($this->acl1->hasPermissionTo('read', 'project', $conditionMet->reveal()));
        $this->assertTrue($this->acl2->hasPermissionTo('write', 'project', $conditionMet->reveal()));
        $this->assertTrue($this->acl3->hasPermissionTo('delete', 'project', $conditionMet->reveal()));
        $this->assertTrue($this->acl4->hasPermissionTo('update', 'project', $conditionMet->reveal()));

        $this->assertFalse($this->acl1->hasPermissionTo('read', 'project', $conditionNotMet->reveal()));
        $this->assertFalse($this->acl2->hasPermissionTo('write', 'project', $conditionNotMet->reveal()));
        $this->assertFalse($this->acl3->hasPermissionTo('delete', 'project', $conditionNotMet->reveal()));
        $this->assertFalse($this->acl4->hasPermissionTo('update', 'project', $conditionNotMet->reveal()));

        $this->assertFalse($this->acl1->hasPermissionTo('write', 'project'));
        $this->assertFalse($this->acl2->hasPermissionTo('delete', 'project'));
        $this->assertFalse($this->acl3->hasPermissionTo('update', 'project'));
        $this->assertFalse($this->acl4->hasPermissionTo('read', 'project'));

        $this->assertFalse($this->acl1->hasPermissionTo('write', 'project', $conditionMet->reveal()));
        $this->assertFalse($this->acl2->hasPermissionTo('delete', 'project', $conditionMet->reveal()));
        $this->assertFalse($this->acl3->hasPermissionTo('update', 'project', $conditionMet->reveal()));
        $this->assertFalse($this->acl4->hasPermissionTo('read', 'project', $conditionMet->reveal()));

        $this->assertFalse($this->acl1->hasPermissionTo('write', 'project', $conditionNotMet->reveal()));
        $this->assertFalse($this->acl2->hasPermissionTo('delete', 'project', $conditionNotMet->reveal()));
        $this->assertFalse($this->acl3->hasPermissionTo('update', 'project', $conditionNotMet->reveal()));
        $this->assertFalse($this->acl4->hasPermissionTo('read', 'project', $conditionNotMet->reveal()));
    }
}
