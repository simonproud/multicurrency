<?php
declare(strict_types=1);

namespace Simonproud\Multicurrency;

interface LockManagerInterface
{
    public function acquire(string $key): bool;
    public function release(string $key): void;
}
