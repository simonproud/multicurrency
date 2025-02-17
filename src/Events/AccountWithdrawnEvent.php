<?php
declare(strict_types=1);

namespace Simonproud\Multicurrency\Events;

use Simonproud\Multicurrency\Account;
use Simonproud\Multicurrency\CurrencyInterface;
use Simonproud\Multicurrency\Event;

class AccountWithdrawnEvent implements Event
{
    public const NAME = 'account.withdrawn';
    private Account $account;
    private CurrencyInterface $currency;
    private float $amount;

    public function __construct(Account $account, CurrencyInterface $currency, float $amount)
    {
        $this->account = $account;
        $this->currency = $currency;
        $this->amount = $amount;
    }

    public function getAccount(): Account
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
