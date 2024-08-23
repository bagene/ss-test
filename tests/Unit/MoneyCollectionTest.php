<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\Currency;
use App\Money\Money;
use App\Money\MoneyCollection;
use PHPUnit\Framework\TestCase;

final class MoneyCollectionTest extends TestCase
{
    public function testGetter(): void
    {
        $money1 = Money::fromInt(100, Currency::EUR);
        $money2 = Money::fromInt(200, Currency::EUR);
        $moneyCollection = new MoneyCollection([
            $money1,
            $money2,
        ]);

        $this->assertEquals(2, $moneyCollection->count());
        $this->assertEquals([
            $money1,
            $money2,
        ], $moneyCollection->getMoneys());
    }

    public function testNamedConstructor(): void
    {
        $money1 = Money::fromInt(100, Currency::EUR);
        $money2 = Money::fromInt(200, Currency::EUR);
        $moneyCollection = MoneyCollection::from($money1, $money2);

        $this->assertEquals(2, $moneyCollection->count());
        $this->assertEquals([
            $money1,
            $money2,
        ], $moneyCollection->getMoneys());
    }

    public function testGetLowest(): void
    {
        $money1 = Money::fromInt(100, Currency::EUR);
        $money2 = Money::fromInt(200, Currency::EUR);
        $moneyCollection = MoneyCollection::from($money1, $money2);

        $this->assertEquals($money1, $moneyCollection->getLowest());
    }

    public function testGetHighest(): void
    {
        $money1 = Money::fromInt(100, Currency::EUR);
        $money2 = Money::fromInt(200, Currency::EUR);
        $moneyCollection = MoneyCollection::from($money1, $money2);

        $this->assertEquals($money2, $moneyCollection->getHighest());
    }

    public function testGetAverage(): void
    {
        $money1 = Money::fromInt(100, Currency::EUR);
        $money2 = Money::fromInt(200, Currency::EUR);
        $moneyCollection = MoneyCollection::from($money1, $money2);

        $this->assertEquals(15000, $moneyCollection->getAverage()->getAmount());
        $this->assertEquals(150, $moneyCollection->getAverage()->getFloatValue());
    }
}
