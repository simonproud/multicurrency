<?php
declare(strict_types=1);

namespace Simonproud\Multicurrency;

use Simonproud\Multicurrency\Events\AccountDepositedEvent;
use Simonproud\Multicurrency\Events\AccountWithdrawnEvent;

class Account
{
    private BalanceInterface $balance;
    private CurrencyInterface $primaryCurrency;
    private CurrencyRateInterface $currencyRate;
    private array $supportedCurrencies = [];
    private ?EventDispatcherInterface $eventDispatcher = null;
    private ?LockManagerInterface $lockManager = null;

    public function __construct(CurrencyRateInterface $currencyRate, BalanceInterface $balance)
    {
        $this->balance = $balance;
        $this->currencyRate = $currencyRate;
    }

    public function addCurrency(CurrencyInterface $currency): void
    {
        $this->supportedCurrencies[] = $currency;
    }

    public function setPrimaryCurrency(CurrencyInterface $currency): void
    {
        if (!in_array($currency, $this->supportedCurrencies, true)) {
            throw new CurrencyException();
        }
        $this->primaryCurrency = $currency;
    }

    /**
     * Set the lock manager to be used for transaction safety.
     */
    public function setLockManager(LockManagerInterface $lockManager): void
    {
        $this->lockManager = $lockManager;
    }

    /**
     * Deposit funds with transaction locking.
     *
     * @param CurrencyInterface $currency
     * @param float $amount
     * @param string $transactionId Unique identifier for this transaction.
     * @return void
     */
    public function deposit(CurrencyInterface $currency, float $amount, string $transactionId = ''): void
    {
        if ($transactionId !== '' && $this->lockManager !== null) {
            if (!$this->lockManager->acquire($transactionId)) {
                throw new LockException('Duplicate deposit transaction: ' . $transactionId);
            }
        }

        try {
            $this->balance->add($currency, $amount);
            if ($this->eventDispatcher !== null) {
                $this->eventDispatcher->dispatch(
                    AccountDepositedEvent::NAME,
                    new AccountDepositedEvent($this, $currency, $amount)
                );
            }
        } finally {
            if ($transactionId !== '' && $this->lockManager !== null) {
                $this->lockManager->release($transactionId);
            }
        }
    }

    /**
     * Withdraw funds with transaction locking.
     *
     * @param Currency $currency
     * @param float $amount
     * @param string $transactionId Unique identifier for this transaction.
     * @return void
     */
    public function withdraw(CurrencyInterface $currency, float $amount, string $transactionId = ''): void
    {
        if ($transactionId !== '' && $this->lockManager !== null) {
            if (!$this->lockManager->acquire($transactionId)) {
                throw new LockException('Duplicate withdraw transaction: ' . $transactionId);
            }
        }

        try {
            $this->balance->subtract($currency, $amount);
            if ($this->eventDispatcher !== null) {
                $this->eventDispatcher->dispatch(
                    AccountWithdrawnEvent::NAME,
                    new AccountWithdrawnEvent($this, $currency, $amount)
                );
            }
        } finally {
            if ($transactionId !== '' && $this->lockManager !== null) {
                $this->lockManager->release($transactionId);
            }
        }
    }

    public function getBalance(CurrencyInterface $currency = null): float
    {
        if ($currency === null) {
            $currency = $this->primaryCurrency;
        }
        return $this->balance->get($currency);
    }

    public function convertBalance(CurrencyInterface $from, CurrencyInterface $to, float $amount): float
    {
        $rate = $this->currencyRate->getRate($from, $to);
        return $amount * $rate;
    }

    public function getSupportedCurrencies(): array
    {
        return $this->supportedCurrencies;
    }

    public function disableCurrency(CurrencyInterface $currency): void
    {
        $this->supportedCurrencies = array_filter($this->supportedCurrencies, fn($c) => $c !== $currency);
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->eventDispatcher = $dispatcher;
    }
}
