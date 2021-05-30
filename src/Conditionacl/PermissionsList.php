<?php

declare(strict_types=1);

namespace Conditionacl;

class PermissionsList implements \Countable
{
    /** @var Permission[] */
    protected array $list = [];

    private function getPermissionHash(Permission $permission): string
    {
        return $permission->getOperation().':'.$permission->getSubject();
    }

    /**
     * @param Permission[] $list
     */
    public function __construct(array $list = [])
    {
        foreach ($list as $item) {
            $this->add($item);
        }
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function add(Permission $permission): void
    {
        $this->list[$this->getPermissionHash($permission)] = $permission;
    }

    public function remove(Permission $permission): void
    {
        unset($this->list[$this->getPermissionHash($permission)]);
    }

    public function has(string $operation, string $subject): bool
    {
        $hash = $this->getPermissionHash(new Permission($operation, $subject));

        return \array_key_exists($hash, $this->list) && !empty($this->list[$hash]);
    }

    public function merge(self $anotherList): self
    {
        return new self(array_merge($this->list, $anotherList->getList()));
    }

    public function diff(self $anotherList): self
    {
        $diff = \array_udiff(
            $this->list,
            $anotherList->getList(),
            fn ($a, $b): int => strcasecmp($this->getPermissionHash($a), $this->getPermissionHash($b))
        );

        return new self($diff);
    }

    public function count(): int
    {
        return \count($this->list);
    }
}
