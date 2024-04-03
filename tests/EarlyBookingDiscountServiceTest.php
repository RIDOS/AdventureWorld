<?php

namespace App\Tests;

use App\Service\EarlyBookingDiscountService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Тест сервиса для расчета скидки за ранее бронирование.
 */
class EarlyBookingDiscountServiceTest extends TestCase
{
    public function testCalculateEarlyBookingDiscountForMay2027Start()
    {
        // Подготовка данных.
        $startDate = new DateTimeImmutable('2027-05-01');
        $paymentDate = new DateTimeImmutable('2026-11-30');
        $price = 20000; // Примерная стоимость путешествия

        // Создание экземпляра сервиса.
        $discountService = new EarlyBookingDiscountService($startDate, $paymentDate, $price);

        // Рассчёт скидки.
        $actualDiscount = $discountService->calculateEarlyBookingDiscount();

        // Проверка, что скидка корректна. Для даты начала путешествия 1 мая 2027 года и даты оплаты 30 ноября 2026,
        // скидка должна быть 7% от стоимости, но не более 1500 рублей.
        $expectedDiscount = min($price * 0.07, 1500);
        $this->assertEquals($expectedDiscount, $actualDiscount);
    }

    public function testCalculateEarlyBookingDiscountForJanuary2027Start()
    {
        $startDate = new DateTimeImmutable('2027-01-15');
        $paymentDates = [
            new DateTimeImmutable('2026-08-30'), // 7%
            new DateTimeImmutable('2026-09-28'), // 5%
            new DateTimeImmutable('2026-10-30'), // 3%
        ];

        // Примерная стоимость путешествия.
        $price = 172000;

        // Ожидаемые скидки в рублях.
        $expectedDiscounts = [172000 * 0.07, 172000 * 0.05, 172000 * 0.03];

        foreach ($paymentDates as $index => $paymentDate) {
            $discount = (new EarlyBookingDiscountService(
                startDate: $startDate,
                paymentDate: $paymentDate,
                price: $price
            ))->calculateEarlyBookingDiscount();
            $this->assertEquals($expectedDiscounts[$index], $discount);
        }
    }
}
