<?php
declare(strict_types=1);
namespace Simonproud\Multicurrency;

class Currency implements CurrencyInterface
{
    private string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
