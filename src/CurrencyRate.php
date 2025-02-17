<?php
declare(strict_types=1);
namespace Simonproud\Multicurrency;

class CurrencyRate implements CurrencyRateInterface
{
    private array $rates = [];

    public function setRate(CurrencyInterface $from, CurrencyInterface $to, float $rate): void
    {
        $this->rates[$from->getCode()][$to->getCode()] = $rate;
    }

    public function getRate(CurrencyInterface $from, CurrencyInterface $to): float
    {
        if (isset($this->rates[$from->getCode()][$to->getCode()])) {
            return $this->rates[$from->getCode()][$to->getCode()];
        }
        throw new \Exception("Rate not found.");
    }
}
