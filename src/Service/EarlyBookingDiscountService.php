<?php

namespace App\Service;

use DateTimeImmutable;

/**
 * Сервис для расчета скидки ща ранее бронирование.
 *
 * Этот сервис предназначен для расчета скидки за ранее бронирование в зависимости от
 * даты оплаты и даты начала путешествия. Он принимает на вход дату начала путешествия,
 * дату оплаты и стоимость путешествия с учетом детской скидки, а затем рассчитывает
 * скидку в соответствии с заданными условиями.
 *
 * @author Imaev Azat
 */
class EarlyBookingDiscountService
{
    /**
     * @var DateTimeImmutable $startDate Дата начала путешествия.
     */
    private DateTimeImmutable $startDate;

    /**
     * @var DateTimeImmutable $paymentDate Дата оплаты.
     */
    private DateTimeImmutable $paymentDate;

    /**
     * @var float $price Стоимость путешествия с учетом детской скидки.
     */
    private float $price;

    public function __construct(DateTimeImmutable $startDate, DateTimeImmutable $paymentDate, float $price)
    {
        $this->startDate = $startDate;
        $this->paymentDate = $paymentDate;
        $this->price = $price;
    }

    /**
     * Рассчитывает скидку за ранее бронирование в зависимости от даты оплаты и даты начала путешествия.
     *
     * TODO: Исправить метод.
     * @return float Сумма со скидкой.
     */
    public function calculateEarlyBookingDiscount(): float
    {
        $startMonth = (int) $this->startDate->format('n');
        $startDay = (int) $this->startDate->format('j'); // Добавлено определение дня старта
        $paymentMonth = (int) $this->paymentDate->format('n');

        $startYear = (int) $this->startDate->format('Y');
        $paymentYear = (int) $this->paymentDate->format('Y');

        $discountRate = 0;

        // Для путешествий с датой старта с 1 апреля по 30 сентября следующего года
        if ($startMonth >= 4 && $startMonth <= 9 && $paymentYear === $startYear - 1) {
            if ($paymentMonth === 12) {
                $discountRate = 0.05;
            } elseif ($paymentMonth === 1) {
                $discountRate = 0.03;
            }
        }
        // Путешествие с датой старта с 1 октября текущего года по 14 января следующего года
        elseif (($startMonth === 10 || $startMonth === 11 || $startMonth === 12 || $startMonth === 1 || ($startMonth === 2 && $startDay <= 14)) && $paymentYear === $startYear) {
            if ($paymentMonth <= 3) {
                $discountRate = 0.07;
            } elseif ($paymentMonth === 4) {
                $discountRate = 0.05;
            } elseif ($paymentMonth === 5) {
                $discountRate = 0.03;
            }
        }
        // Путешествие с датой старта с 15 января следующего года и далее
        elseif ($startMonth >= 1 && $paymentYear === $startYear - 1) {
            if ($paymentMonth <= 8) {
                $discountRate = 0.07;
            } elseif ($paymentMonth === 9) {
                $discountRate = 0.05;
            } elseif ($paymentMonth === 10) {
                $discountRate = 0.03;
            }
        }

        // Возвращаем размер скидки
        return min($this->price * $discountRate, 1500);
    }
}
