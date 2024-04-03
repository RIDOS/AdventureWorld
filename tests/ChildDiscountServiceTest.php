<?php

namespace App\Tests;

use App\Service\ChildDiscountService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Тест сервиса расчета детских скидок.
 *
 * @author Imaev Azat.
 */
class ChildDiscountServiceTest extends KernelTestCase
{
    public function testCalculateDiscount()
    {
        // Массив данных для тестирования: возраст, базовая цена, ожидаемая скидка
        $testCases = [
            ['age' => 5, 'basePrice' => 10000, 'expectedDiscount' => 8000],
            ['age' => 7, 'basePrice' => 3500, 'expectedDiscount' => 2450],
            ['age' => 7, 'basePrice' => 10000, 'expectedDiscount' => 4500],
            ['age' => 14, 'basePrice' => 12750, 'expectedDiscount' => 11475],
            ['age' => 2, 'basePrice' => 2000, 'expectedDiscount' => 0],
            ['age' => 4, 'basePrice' => 2450.50, 'expectedDiscount' => 1960.40],
            ['age' => 11, 'basePrice' => 6000, 'expectedDiscount' => 4200],
            ['age' => 17, 'basePrice' => 8000, 'expectedDiscount' => 7200],
        ];

        foreach ($testCases as $testCase) {
            $age = $testCase['age'];
            $basePrice = $testCase['basePrice'];
            $expectedDiscount = $testCase['expectedDiscount'];

            $childDiscountService = new ChildDiscountService(age: $age, basePrice: $basePrice);
            $actualDiscount = $childDiscountService->calculateDiscount($age, $basePrice);

            $this->assertEquals($expectedDiscount, $actualDiscount);
        }
    }
}
