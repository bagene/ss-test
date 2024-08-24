<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\Currency;
use App\Money\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testFromFloat(): void
    {
        $money = Money::fromFloat(10.5, Currency::EUR);

        $this->assertEquals(1050, $money->getAmount());
        $this->assertEquals(Currency::EUR, $money->getCurrency());
    }

    public function testFromInt(): void
    {
        $money = Money::fromInt(10, Currency::EUR);

        $this->assertEquals(1000, $money->getAmount());
        $this->assertEquals(Currency::EUR, $money->getCurrency());

        $money = Money::fromInt(10, Currency::EUR, false);

        $this->assertEquals(10, $money->getAmount());
        $this->assertEquals(Currency::EUR, $money->getCurrency());
    }

    public function testGetFloatValue(): void
    {
        $money = Money::fromInt(10501, Currency::EUR, false);

        $this->assertEquals(105.01, $money->getFloatValue());

        $money->toMinorUnits();

        $this->assertEquals(10501, $money->getFloatValue());
    }

    public function testConvertTo(): void
    {
        // see App\Money\Conversion for conversion rates
        $money = Money::fromInt(100, Currency::AMD);
        $money->convertTo(Currency::EUR);

        $this->assertEquals(9207, $money->getAmount());
        $this->assertEquals(92.07, $money->getFloatValue());
        $this->assertEquals(Currency::EUR, $money->getCurrency());
    }

    public function testAdd(): void
    {
        $money1 = Money::fromInt(100, Currency::EUR);
        $money2 = Money::fromInt(200, Currency::EUR);

        $result = $money1->add($money2);

        $this->assertEquals(30000, $result->getAmount());
        $this->assertEquals(Currency::EUR, $result->getCurrency());

        // test with different currencies
        $money2->convertTo(Currency::AMD);

        $result = $money1->add($money2);

        $this->assertEquals(30000, $result->getAmount());
        $this->assertEquals(Currency::EUR, $result->getCurrency());
    }

    public function testSubtract(): void
    {
        $money1 = Money::fromInt(200, Currency::EUR);
        $money2 = Money::fromInt(100, Currency::EUR);

        $result = $money1->subtract($money2);

        $this->assertEquals(10000, $result->getAmount());
        $this->assertEquals(Currency::EUR, $result->getCurrency());

        // test with different currencies
        $money2->convertTo(Currency::AMD);

        $result = $money1->subtract($money2);

        $this->assertEquals(10000, $result->getAmount());
        $this->assertEquals(Currency::EUR, $result->getCurrency());
    }

    public function testMultiply(): void
    {
        $money = Money::fromInt(100, Currency::EUR);
        $result = $money->multiply(2);

        $this->assertEquals(20000, $result->getAmount());
        $this->assertEquals(Currency::EUR, $result->getCurrency());
    }

    public function testMultiplyByMoney(): void
    {
        $money1 = Money::fromInt(100, Currency::EUR);
        $money2 = Money::fromInt(2, Currency::EUR);
        $result = $money1->multiplyByMoney($money2);

        $this->assertEquals(20000, $result->getAmount());
        $this->assertEquals(Currency::EUR, $result->getCurrency());

        // test with different currencies
        $money2->convertTo(Currency::AMD);
        $result = $money1->multiplyByMoney($money2);

        $this->assertEquals(20000, $result->getAmount());
        $this->assertEquals(Currency::EUR, $result->getCurrency());
    }

    public function testDivide(): void
    {
        $money = Money::fromInt(200, Currency::EUR);
        $result = $money->divide(2);

        $this->assertEquals(10000, $result->getAmount());
        $this->assertEquals(Currency::EUR, $result->getCurrency());
    }

    public function testDivideByMoney(): void
    {
        $money1 = Money::fromInt(200, Currency::EUR);
        $money2 = Money::fromInt(2, Currency::EUR);
        $result = $money1->divideByMoney($money2);

        $this->assertEquals(10000, $result->getAmount());
        $this->assertEquals(Currency::EUR, $result->getCurrency());

        // test with different currencies
        $money2->convertTo(Currency::AMD);
        $result = $money1->divideByMoney($money2);

        $this->assertEquals(10000, $result->getAmount());
        $this->assertEquals(Currency::EUR, $result->getCurrency());
    }

    public function testToString(): void
    {
            $money = Money::fromInt(100, Currency::EUR);

        $this->assertEquals('€100.00', $money->toString());
    }
}
