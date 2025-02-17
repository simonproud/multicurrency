<?php
declare(strict_types=1);

namespace Simonproud\Multicurrency;

interface CurrencyRateInterface
{
    public function setRate(CurrencyInterface $from, CurrencyInterface $to, float $rate): void;
    public function getRate(CurrencyInterface $from, CurrencyInterface $to): float;
}
