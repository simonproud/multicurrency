<?php
declare(strict_types=1);
namespace Simonproud\Multicurrency;

class Bank
{
    private array $accounts = [];

    public function openAccount(CurrencyRate $currencyRate): Account
    {
        $account = new Account($currencyRate, new Balance());
        $this->accounts[] = $account;
        return $account;
    }
}
