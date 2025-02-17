<?php
declare(strict_types=1);

namespace Simonproud\Multicurrency;


interface AccountInterface
{
    public function addCurrency(CurrencyInterface $currency): void;
    public function setPrimaryCurrency(CurrencyInterface $currency): void;
    public function deposit(CurrencyInterface $currency, float $amount): void;
    public function withdraw(CurrencyInterface $currency, float $amount): void;
    public function getBalance(CurrencyInterface $currency = null): float;
    public function convertBalance(
        CurrencyInterface $from,
        CurrencyInterface $to,
        float $amount
    ): float;
    public function getSupportedCurrencies(): array;
    public function disableCurrency(CurrencyInterface $currency): void;
}
