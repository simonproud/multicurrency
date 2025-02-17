<?php
declare(strict_types=1);
namespace Simonproud\Multicurrency;

class BankExample
{
    private array $accounts = [];

    public function openAccount(CurrencyRateInterface $currencyRate): AccountInterface
    {
        $account = new Account($currencyRate, new Balance());
        $this->accounts[] = $account;
        return $account;
    }
}
