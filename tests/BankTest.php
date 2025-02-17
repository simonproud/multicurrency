<?php
declare(strict_types=1);

namespace Simonproud\Multicurrency\Tests;

use PHPUnit\Framework\TestCase;
use Simonproud\Multicurrency\Account;
use Simonproud\Multicurrency\Bank;
use Simonproud\Multicurrency\Currency;
use Simonproud\Multicurrency\CurrencyRate;
use Simonproud\Multicurrency\EventDispatcher;
use Simonproud\Multicurrency\Events\AccountDepositedEvent;
use Simonproud\Multicurrency\Events\AccountWithdrawnEvent;

class BankTest extends TestCase
{
    private CurrencyRate $rate;
    private Currency $usd;
    private Currency $eur;
    private Bank $bank;
    private Account $account;

    protected function setUp(): void
    {
        $this->rate = new CurrencyRate();
        $this->usd = new Currency("USD");
        $this->eur = new Currency("EUR");

        $this->rate->setRate($this->usd, $this->eur, 0.9);
        $this->rate->setRate($this->eur, $this->usd, 1.1);

        $this->bank = new Bank();
        $this->account = $this->bank->openAccount($this->rate);

        $this->account->addCurrency($this->usd);
        $this->account->addCurrency($this->eur);

        $this->account->setPrimaryCurrency($this->usd);
    }

    public function testDepositAndBalance(): void
    {
        $this->account->deposit($this->usd, 100);
        $this->assertEquals(100, $this->account->getBalance($this->usd));

        $this->account->deposit($this->eur, 50);
        $this->assertEquals(50, $this->account->getBalance($this->eur));
    }

    public function testWithdraw(): void
    {
        $this->account->deposit($this->usd, 200);
        $this->account->withdraw($this->usd, 50);
        $this->assertEquals(150, $this->account->getBalance($this->usd));
    }

    public function testInsufficientFundsInWithdraw(): void
    {
        $this->account->deposit($this->usd, 20);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Insufficient funds.");
        $this->account->withdraw($this->usd, 30);
    }

    public function testConversion(): void
    {
        $this->account->deposit($this->usd, 100);
        $converted = $this->account->convertBalance($this->usd, $this->eur, 100);
        $this->assertEquals(90, $converted);
    }

    public function testUnsupportedPrimaryCurrency(): void
    {
        $gbp = new Currency("GBP");
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Currency not supported.");
        $this->account->setPrimaryCurrency($gbp);
    }

    public function testDisableCurrency(): void
    {
        $this->account->disableCurrency($this->eur);
        $supported = $this->account->getSupportedCurrencies();
        foreach ($supported as $currency) {
            $this->assertNotEquals("EUR", $currency->getCode());
        }
    }

    public function testMissingCurrencyRate(): void
    {
        $gbp = new Currency("GBP");
        $this->account->addCurrency($gbp);
        $this->account->deposit($gbp, 100);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Rate not found.");
        $this->account->convertBalance($gbp, $this->usd, 100);
    }

    public function testDepositTriggersEvent(): void
    {
        $dispatcher = new EventDispatcher();

        $capturedEvent = null;
        $dispatcher->addListener('account.deposited', function ($event) use (&$capturedEvent) {
            $capturedEvent = $event;
        });

        $this->account->setEventDispatcher($dispatcher);
        $this->account->deposit($this->usd, 150);

        $this->assertInstanceOf(AccountDepositedEvent::class, $capturedEvent);
        $this->assertSame($this->account, $capturedEvent->getAccount());
        $this->assertSame($this->usd, $capturedEvent->getCurrency());
        $this->assertEquals(150, $capturedEvent->getAmount());
    }

    public function testWithdrawTriggersEvent(): void
    {
        $dispatcher = new EventDispatcher();

        $capturedEvent = null;
        $dispatcher->addListener('account.withdrawn', function ($event) use (&$capturedEvent) {
            $capturedEvent = $event;
        });

        $this->account->deposit($this->usd, 200);
        $this->account->setEventDispatcher($dispatcher);
        $this->account->withdraw($this->usd, 70);

        $this->assertInstanceOf(AccountWithdrawnEvent::class, $capturedEvent);
        $this->assertSame($this->account, $capturedEvent->getAccount());
        $this->assertSame($this->usd, $capturedEvent->getCurrency());
        $this->assertEquals(70, $capturedEvent->getAmount());
    }
}
