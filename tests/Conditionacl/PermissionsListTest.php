<?php

declare(strict_types=1);

namespace Conditionacl;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PermissionsListTest extends TestCase
{
    private PermissionsList $list1;
    private PermissionsList $list2;
    private PermissionsList $list3;
    private PermissionsList $list4;

    protected function setUp(): void
    {
        parent::setUp();

        $this->list1 = new PermissionsList([
            new Permission('read', 'project'),
        ]);

        $this->list2 = new PermissionsList([
            new Permission('read', 'project'),
            new Permission('write', 'project'),
        ]);

        $this->list3 = new PermissionsList([
            new Permission('read', 'project'),
            new Permission('write', 'project'),
            new Permission('delete', 'project'),
        ]);

        $this->list4 = new PermissionsList([
            new Permission('write', 'project'),
            new Permission('delete', 'project'),
            new Permission('update', 'project'),
        ]);
    }

    public function testRemove(): void
    {
        $this->list1->remove(new Permission('read', 'project'));
        $this->list2->remove(new Permission('read', 'project'));
        $this->list3->remove(new Permission('read', 'project'));
        $this->list4->remove(new Permission('read', 'project'));

        static::assertEquals([], $this->list1->getList());
        static::assertEquals(['write:project' => new Permission('write', 'project')], $this->list2->getList());
        static::assertEquals(
            [
                'write:project' => new Permission('write', 'project'),
                'delete:project' => new Permission('delete', 'project'),
            ],
            $this->list3->getList()
        );
        static::assertEquals(
            [
                'write:project' => new Permission('write', 'project'),
                'delete:project' => new Permission('delete', 'project'),
                'update:project' => new Permission('update', 'project'),
            ],
            $this->list4->getList()
        );
    }

    public function testHas(): void
    {
        static::assertTrue($this->list1->has('read', 'project'));
        static::assertTrue($this->list2->has('write', 'project'));
        static::assertTrue($this->list3->has('delete', 'project'));
        static::assertTrue($this->list4->has('update', 'project'));

        static::assertFalse($this->list1->has('write', 'project'));
        static::assertFalse($this->list2->has('delete', 'project'));
        static::assertFalse($this->list3->has('update', 'project'));
        static::assertFalse($this->list4->has('read', 'project'));
    }

    public function testDiff(): void
    {
        $diff1 = $this->list1->diff($this->list2);
        $diff2 = $this->list2->diff($this->list1);
        $diff3 = $this->list4->diff($this->list1);
        $diff4 = $this->list3->diff($this->list2);

        static::assertEquals(new PermissionsList([]), $diff1);
        static::assertEquals(new PermissionsList([new Permission('write', 'project')]), $diff2);
        static::assertEquals(new PermissionsList([
            new Permission('write', 'project'),
            new Permission('delete', 'project'),
            new Permission('update', 'project'),
        ]), $diff3);
        static::assertEquals(new PermissionsList([new Permission('delete', 'project')]), $diff4);
    }

    public function testAdd(): void
    {
        $this->list1->add(new Permission('read', 'project'));
        $this->list2->add(new Permission('read', 'project'));
        $this->list3->add(new Permission('update', 'project'));
        $this->list4->add(new Permission('read', 'project'));

        static::assertEquals(['read:project' => new Permission('read', 'project')], $this->list1->getList());
        static::assertEquals(
            [
                'read:project' => new Permission('read', 'project'),
                'write:project' => new Permission('write', 'project'),
            ],
            $this->list2->getList()
        );
        static::assertEquals(
            [
                'read:project' => new Permission('read', 'project'),
                'write:project' => new Permission('write', 'project'),
                'delete:project' => new Permission('delete', 'project'),
                'update:project' => new Permission('update', 'project'),
            ],
            $this->list3->getList()
        );
        static::assertEquals(
            [
                'read:project' => new Permission('read', 'project'),
                'write:project' => new Permission('write', 'project'),
                'delete:project' => new Permission('delete', 'project'),
                'update:project' => new Permission('update', 'project'),
            ],
            $this->list4->getList()
        );
    }

    public function testMerge(): void
    {
        $merge1 = $this->list1->merge($this->list2);
        $merge2 = $this->list2->merge($this->list3);
        $merge3 = $this->list3->merge($this->list4);
        $merge4 = $this->list1->merge($this->list4);

        static::assertEquals(
            new PermissionsList([
                new Permission('read', 'project'),
                new Permission('write', 'project'),
            ]),
            $merge1
        );

        static::assertEquals(
            new PermissionsList([
                new Permission('read', 'project'),
                new Permission('write', 'project'),
                new Permission('delete', 'project'),
            ]),
            $merge2
        );

        static::assertEquals(
            new PermissionsList([
                new Permission('read', 'project'),
                new Permission('write', 'project'),
                new Permission('delete', 'project'),
                new Permission('update', 'project'),
            ]),
            $merge3
        );

        static::assertEquals(
            new PermissionsList([
                new Permission('read', 'project'),
                new Permission('write', 'project'),
                new Permission('delete', 'project'),
                new Permission('update', 'project'),
            ]),
            $merge4
        );
    }

    public function testCount(): void
    {
        static::assertCount(1, $this->list1->getList());
        static::assertCount(2, $this->list2->getList());
        static::assertCount(3, $this->list3->getList());
        static::assertCount(3, $this->list4->getList());
    }
}
