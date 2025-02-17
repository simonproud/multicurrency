<?php
declare(strict_types=1);

namespace Simonproud\Multicurrency;

class CurrencyException extends \Exception
{
    protected $message = 'Currency not supported.';
}
