<?php
declare(strict_types=1);

namespace Simonproud\Multicurrency\Events;

use Simonproud\Multicurrency\AccountInterface;
use Simonproud\Multicurrency\CurrencyInterface;
use Simonproud\Multicurrency\Event;

class AccountDepositedEvent implements Event
{
    public const NAME = 'account.deposited';
    private AccountInterface $account;
    private CurrencyInterface $currency;
    private float $amount;

    public function __construct(AccountInterface $account, CurrencyInterface $currency, float $amount)
    {
        $this->account = $account;
        $this->currency = $currency;
        $this->amount = $amount;
    }

    public function getAccount(): AccountInterface
    {
        return $this->account;
    }

    public function getCurrency(): CurrencyInterface
    {
        return $this->currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
