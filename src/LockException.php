<?php
declare(strict_types=1);

namespace Simonproud\Multicurrency;

class LockException extends \Exception
{
    protected $message = 'Lock could not be acquired.';
}
