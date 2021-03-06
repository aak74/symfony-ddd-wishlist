<?php

namespace Wishlist\Tests\Domain;

use Money\Currency;
use Money\Money;
use Wishlist\Domain\Expense;
use PHPUnit\Framework\TestCase;

class ExpenseTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider nonsensePriceDataProvider
     */
    public function testPriceAndFeeMustBePositiveNumber($price, $fee, $initialFund)
    {
        Expense::fromCurrencyAndScalars(new Currency('USD'), $price, $fee, $initialFund);
    }

    public function nonsensePriceDataProvider()
    {
        return [
            'Price must be greater than zero' => [0, 0, 0],
            'Fee must be greater than zero' => [1, 0, 0],
            'Price must be positive' => [-1, -1, 0],
            'Fee must be positive' => [1, -1, 0],
            'Initial fund must be positive' => [2, 1, -1],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFeeMustBeLessThanPrice()
    {
        Expense::fromCurrencyAndScalars(new Currency('USD'), 100, 150);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInitialFundMustBeLessThanPrice()
    {
        Expense::fromCurrencyAndScalars(new Currency('USD'), 100, 50, 150);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNewPriceMustBeOfTheSameCurrency()
    {
        $expense = Expense::fromCurrencyAndScalars(new Currency('USD'), 100, 50, 25);

        $expense->changePrice(new Money(200, new Currency('RUB')));
    }

    public function testChangePriceMustReturnANewInstance()
    {
        $expense = Expense::fromCurrencyAndScalars(new Currency('USD'), 100, 50, 25);

        $actual = $expense->changePrice(new Money(200, new Currency('USD')));

        static::assertNotSame($expense, $actual);
        static::assertEquals(200, $actual->getPrice()->getAmount());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNewFeeMustBeOfTheSameCurrency()
    {
        $expense = Expense::fromCurrencyAndScalars(new Currency('USD'), 100, 50, 25);

        $expense->changeFee(new Money(200, new Currency('RUB')));
    }

    public function testChangeFeeMustReturnANewInstance()
    {
        $expense = Expense::fromCurrencyAndScalars(new Currency('USD'), 100, 10, 25);

        $actual = $expense->changeFee(new Money(20, new Currency('USD')));

        static::assertNotSame($expense, $actual);
        static::assertEquals(20, $actual->getFee()->getAmount());
    }
}
