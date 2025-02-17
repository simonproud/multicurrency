<?php
declare(strict_types=1);

namespace Simonproud\Multicurrency;


interface BalanceInterface
{
    public function add(CurrencyInterface $currency, float $amount): void;
    public function subtract(CurrencyInterface $currency, float $amount): void;
    public function get(CurrencyInterface $currency): float;
    public function getAllBalances(): array;
}
