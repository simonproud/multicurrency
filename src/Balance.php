<?php
declare(strict_types=1);
namespace Simonproud\Multicurrency;

class Balance implements BalanceInterface
{
    private array $balances = [];

    public function add(CurrencyInterface $currency, float $amount): void
    {
        if (!isset($this->balances[$currency->getCode()])) {
            $this->balances[$currency->getCode()] = 0;
        }
        $this->balances[$currency->getCode()] += $amount;
    }

    public function subtract(CurrencyInterface $currency, float $amount): void
    {
        if (!isset($this->balances[$currency->getCode()]) || $this->balances[$currency->getCode()] < $amount) {
            throw new \Exception("Insufficient funds.");
        }
        $this->balances[$currency->getCode()] -= $amount;
    }

    public function get(CurrencyInterface $currency): float
    {
        return $this->balances[$currency->getCode()] ?? 0;
    }

    public function getAllBalances(): array
    {
        return $this->balances;
    }
}
