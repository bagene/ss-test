<?php

declare(strict_types=1);

namespace App\Money;

use App\Enums\Currency;

final class Money
{
    private function __construct(
        private int $amount = 0,
        private Currency $currency = Currency::EUR,
    ) {
    }

    public function toMinorUnits(): Money
    {
        $this->amount = $this->amount * pow(10, $this->currency->decimals());

        return $this;
    }

    public static function fromFloat(float $amount, Currency $currency): Money
    {
        return new Money((int) round($amount * pow(10, $currency->decimals())), $currency);
    }

    public static function fromInt(int $amount, Currency $currency, bool $toMinorUnits = true): Money
    {
        $money = new Money($amount, $currency);

        if ($toMinorUnits) {
            $money->toMinorUnits();
        }

        return $money;
    }

    public function getFloatValue(): float
    {
        return round($this->amount / pow(10, $this->currency->decimals()), 2);
    }

    public function convertTo(Currency $currency): Money
    {
        $this->amount = Conversion::convert($this->amount, $this->currency, $currency);
        $this->currency = $currency;

        return $this;
    }

    private function checkAndConvert(Money &$money): void
    {
        if ($this->currency !== $money->currency) {
            $money->convertTo($this->currency);
        }
    }

    public function add(Money $money): Money
    {
        $this->checkAndConvert($money);

        return Money::fromInt(
            $this->amount + $money->getAmount(),
            $this->currency,
            false,
        );
    }

    public function subtract(Money $money): Money
    {
        $this->checkAndConvert($money);

        return Money::fromInt($this->amount - $money->getAmount(),
            $this->currency,
            false,
        );
    }

    public function multiply(int|float $value): Money
    {
        return Money::fromInt(
            (int) ($this->amount * $value),
            $this->currency,
            false,
        );
    }

    public function multiplyByMoney(Money $money): Money
    {
        $this->checkAndConvert($money);

        return Money::fromInt(
            (int) ($this->amount * $money->getFloatValue()),
            $this->currency,
            false,
        );
    }

    public function divide(int|float $value): Money
    {
        return Money::fromInt(
            (int) ($this->amount / $value),
            $this->currency,
            false,
        );
    }

    public function divideByMoney(Money $money): Money
    {
        $this->checkAndConvert($money);

        return Money::fromInt(
            (int) ($this->amount / $money->getFloatValue()),
            $this->currency,
            false,
        );
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function toString(): string
    {
        return $this->currency->symbol() . number_format($this->getFloatValue(), 2);
    }
}
