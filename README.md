## Money Object Test

This is a test of the Money object. The Money object is a simple object that represents a monetary value. It has a value and a currency. The value is a decimal number and the currency is a string. The Money object has a method that allows you to convert the value to a different currency.

### Directory

- app
  - Money
    - Money.php
    - Conversion.php
    - MoneyCollection.php
---

### Lint and Test

- `composer analyze`
- `php artisan test`

---
### Test Endpoint

- `POST /api/orders`

```json
{
    "reference": "ORD-123",
    "items": [
        {
            "name": "Item 1",
            "price": 1.23,
            "currency": "USD",
            "quantity": 2
        },
        {
            "name": "Item 2",
            "price": 4.56,
            "currency": "USD",
            "quantity": 1
        }
    ],
    "discount": 50,
    "discount_type": "percentage",
    "currency": 45
}
```

### Money Object

The Money class uses minor units for value. This means that the value is stored as an integer and the decimal point is implied. For example, $1.23 would be stored as 123. This is done to avoid floating point rounding errors.

&nbsp;
#### Named Constructors

To avoid issues with minor units when creating a Money object, named constructor are used instead of the normal constructor.

* `Money::fromFloat(float $value, string $currency): Money` - Creates a Money object from a float value.
```php
$money = Money::fromFloat(1.23, Currency::fromName('USD')); // $1.23 USD
```
* `Money::fromInt(int $value, string $currency, bool $toMinorUnits = true): Money` - Creates a Money object from a int value. Automatically convert to minor units.
```php
$money = Money::fromInt(1000, Currency::fromName('USD')); // $10.00 USD
$money = Money::fromInt(1000, Currency::fromName('USD'), false); // $1000.00 USD
```
&nbsp;
#### Getters

* `getValue(): int` - Get the value of the Money object in minor units.
```php
$money = Money::fromFloat(1.23, Currency::fromName('USD'));
$value = $money->getValue(); // 123
```
* `getFloatValue(): float` - Get the value of the Money object as a float.
```php
$money = Money::fromFloat(1.23, Currency::fromName('USD'));
$value = $money->getFloatValue(); // 1.23
```
* `toString(): string` - Get the value of the Money object as a formatted string.
```php
$money = Money::fromFloat(1.23, Currency::fromName('USD'));
$value = $money->toString(); // $1.23
```
* `getCurrency(): Currency` - Get the currency of the Money object.

&nbsp;
#### Operators

These operators allow you to perform basic arithmetic operations on Money objects. Using it with multiple currencies automatically convert it to the primary currency before performing the operation.

* `convertTo(string $currency): Money` - Convert the Money object to a different currency.
```php
$money = Money::fromFloat(1.0861, 'AMD');
$money = $money->convertTo('EUR'); // €1.00 EUR
```
* `add(Money $money): Money` - Add two Money objects together.
```php
$money1 = Money::fromFloat(1.23, Currency::fromName('USD'));
$money2 = Money::fromFloat(4.56, Currency::fromName('USD'));
$money3 = $money1->add($money2); // $5.79 USD
```
* `subtract(Money $money): Money` - Subtract one Money object from another.
```php
$money1 = Money::fromFloat(5.79, Currency::fromName('USD'));
$money2 = Money::fromFloat(4.56, Currency::fromName('USD'));
$money3 = $money1->subtract($money2); // $1.23 USD
```
* `multiply(float $value): Money` - Multiply the Money object by a float value.
```php
$money = Money::fromFloat(1.23, Currency::fromName('USD'));
$money = $money->multiply(2); // $2.46 USD
```
* `multiplyByMoney(Money $money): Money` - Multiply the Money object by another Money object.
```php
$money1 = Money::fromFloat(1.23, Currency::fromName('USD'));
$money2 = Money::fromFloat(2, Currency::fromName('USD'));
$money3 = $money1->multiplyByMoney($money2); // $2.46 USD
```
* `divide(float $value): Money` - Divide the Money object by a float value.
```php
$money = Money::fromFloat(1.23, Currency::fromName('USD'));
$money = $money->divide(2); // $0.615 USD
```
* `divideByMoney(Money $money): Money` - Divide the Money object by another Money object.
```php
$money1 = Money::fromFloat(1.23, Currency::fromName('USD'));
$money2 = Money::fromFloat(2, Currency::fromName('USD'));
$money3 = $money1->divideByMoney($money2); // $0.615 USD
```
* `applyDiscount(int|float $discount, string $type = Money::FIXED_DISCOUNT): Money` - Apply a discount to the Money object.
```php
$money = Money::fromFloat(1.23, Currency::fromName('USD'));
$money = $money->applyDiscount(0.5, Money::PERCENTAGE_DISCOUNT); // $0.615 USD
```

---
### Money Collection

The MoneyCollection class is a collection of Money objects. It has a method that allows you to average the values of all the Money objects in the collection.

&nbsp;
#### Getters

* `getMoneys(): array` - Get the Money objects in the collection.
* `count(): int` - Get the number of Money objects in the collection.

&nbsp;
#### Methods

* `getLowest(): Money` - Get the Money object with the lowest value in the collection.
```php
$money1 = Money::fromFloat(1.23, Currency::fromName('USD'));
$money2 = Money::fromFloat(4.56, Currency::fromName('USD'));
$money3 = Money::fromFloat(7.89, Currency::fromName('USD'));
$collection = MoneyCollection::from($money1, $money2, $money3);
$money = $collection->getLowest(); // $1.23 USD
```
* `getHighest(): Money` - Get the Money object with the highest value in the collection.
```php
$money1 = Money::fromFloat(1.23, Currency::fromName('USD'));
$money2 = Money::fromFloat(4.56, Currency::fromName('USD'));
$money3 = Money::fromFloat(7.89, Currency::fromName('USD'));
$collection = MoneyCollection::from($money1, $money2, $money3);
$money = $collection->getHighest(); // $7.89 USD
```
* `getAverage(): Money` - Get the average value of all the Money objects in the collection.
```php
$money1 = Money::fromFloat(1.23, Currency::fromName('USD'));
$money2 = Money::fromFloat(4.56, Currency::fromName('USD'));
$money3 = Money::fromFloat(7.89, Currency::fromName('USD'));
$collection = MoneyCollection::from($money1, $money2, $money3);
$money = $collection->getAverage(); // $4.23 USD
```
