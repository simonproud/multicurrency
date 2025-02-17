<?php
declare(strict_types=1);

namespace Simonproud\Multicurrency;

interface CurrencyInterface
{
    public function getCode(): string;
}
