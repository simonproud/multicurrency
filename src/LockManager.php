<?php
declare(strict_types=1);

namespace Simonproud\Multicurrency;

class LockManager implements LockManagerInterface
{
    /**
     * Array of acquired locks.
     *
     * @var array<string, bool>
     */
    private array $locks = [];

    /**
     * Try to acquire a lock with a given key.
     *
     * @param string $key Unique identifier for the transaction.
     * @return bool True if lock acquired, false if already locked.
     */
    public function acquire(string $key): bool
    {
        if (isset($this->locks[$key])) {
            return false;
        }
        $this->locks[$key] = true;
        return true;
    }

    /**
     * Release a lock with a given key.
     *
     * @param string $key
     * @return void
     */
    public function release(string $key): void
    {
        unset($this->locks[$key]);
    }
}
