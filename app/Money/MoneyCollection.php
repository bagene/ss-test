<?php

declare(strict_types=1);

namespace App\Money;

use App\Enums\Currency;

final class MoneyCollection
{
    public function __construct(
        private readonly array $moneys = [],
    ) {
    }

    public static function from(Money ...$moneys): MoneyCollection
    {
        return new MoneyCollection($moneys);
    }

    public function getMoneys(): array
    {
        return $this->moneys;
    }

    public function count(): int
    {
        return count($this->moneys);
    }

    private function convertAllToEuro(): array
    {
        return array_map(fn (Money $money) => $money->convertTo(Currency::EUR), $this->moneys);
    }

    private function getEuroValueInFloat(): array
    {
        return array_map(fn (Money $money) => $money->getFloatValue(), $this->convertAllToEuro());
    }

    public function getLowest(): Money
    {
        $inEuro = $this->getEuroValueInFloat();
        $minIndex = array_keys($inEuro, min($inEuro))[0];

        return $this->moneys[$minIndex];
    }

    public function getHighest(): Money
    {
        $inEuro = $this->getEuroValueInFloat();
        $maxIndex = array_keys($inEuro, max($inEuro))[0];

        return $this->moneys[$maxIndex];
    }

    public function getAverage(Currency $currency = Currency::EUR): Money
    {
        $inEuro = $this->getEuroValueInFloat();
        $average = array_sum($inEuro) / count($inEuro);

        // since EUR was the main currency for calculation
        // instantiate Money with EUR currency
        // and convert it to the desired currency
        $money = Money::fromInt((int) $average, Currency::EUR);
        $money->convertTo($currency);

        return $money;
    }
}
