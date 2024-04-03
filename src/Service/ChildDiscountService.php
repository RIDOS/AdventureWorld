<?php

namespace App\Service;

/**
 * Сервис для расчета детских скидок в зависимости от возраста ребенка.
 *
 * Этот сервис предоставляет метод для расчета скидки на услуги для детей в зависимости от их возраста.
 *
 * @author Imaev Azat
 */
class ChildDiscountService
{
    private const DISCOUNT_AGE_0_3 = 0;
    private const DISCOUNT_AGE_3_6 = 0.8;
    private const DISCOUNT_AGE_6_12 = 0.7;
    private const MAX_DISCOUNT_AGE_6_12 = 4500;
    private const DEFAULT_DISCOUNT = 0.9;

    /**
     * @var int Возраст ребенка.
     */
    private int $age;

    /**
     * @var float Базовая цена путешествия.
     */
    private float $basePrice;


    public function __construct(int $age, float $basePrice)
    {
        $this->age = $age;
        $this->basePrice = $basePrice;
    }

    /**
     * Рассчитывает цену со скидкой для ребенка в зависимости от его возраста и базовой цены.
     *
     * С 3 до 6 лет, скидка 80%,
     * С 6 до 12 лет, скидка 30%, но не более 4.500 ₽,
     * С 12 и до 18 лет, скидка 10%.
     *
     * @return float Цена со скидкой.
     * @throws \InvalidArgumentException Если возраст ребенка меньше 0 или больше или равен 18.
     */
    public function calculateDiscount(): float
    {
        $this->validateChileAge(age: $this->age);

        return match(true) {
            $this->age < 3 => self::DISCOUNT_AGE_0_3,
            $this->age < 6 => $this->basePrice * self::DISCOUNT_AGE_3_6,
            $this->age < 12 => min($this->basePrice * self::DISCOUNT_AGE_6_12, self::MAX_DISCOUNT_AGE_6_12),
            default => $this->basePrice * self::DEFAULT_DISCOUNT,
        };
    }

    /**
     * Проверяет корректность возраста ребенка.
     *
     * @param int $age Возраст ребенка.
     * @throws \InvalidArgumentException Если возраст ребенка меньше 0 или больше или равен 18.
     */
    private function validateChileAge(int $age): void
    {
        if ($age < 0) {
            throw new \InvalidArgumentException("Возраст ребенка не может быть отрицательным.");
        }

        if ($age >= 18) {
            throw new \InvalidArgumentException("Возраст ребенка не может быть больше 17 лет.");
        }
    }
}
