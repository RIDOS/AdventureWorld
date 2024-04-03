<?php

namespace App\Controller;

use App\Service\ChildDiscountService;
use App\Service\EarlyBookingDiscountService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Контроллер для расчета стоимости путешествия с учетом скидок.
 *
 * Этот контроллер обрабатывает запросы на расчет стоимости путешествия с учетом
 * возраста участника и других параметров. Он принимает данные через HTTP запросы,
 * проверяет их на корректность и валидность, а затем вычисляет общую стоимость
 * путешествия с учетом скидок, если они применимы.
 *
 * @author Imaev Azat
 */
class TravelCostWithDiscountController extends AbstractController
{
    /**
     * Стоймость путешествия с учётом скидок.
     *
     * @param Request $request Передаваемые параметры.
     * @return JsonResponse Результат подсчета скидки.
     */
    #[Route('/api/calculate_travel_with_discount', name: 'app_travel_cost_with_discount', methods: ['POST'])]
    public function index(Request $request): JsonResponse
    {
        $params = json_decode($request->getContent(), true);

        if ($params === null) {
            return $this->json([
                'status' => 'error',
                'message' => 'Неверный формат JSON'
            ], 400);
        }

        $validationErrors = $this->validateParams($params);

        // Обработка ошибок валидации.
        if (count($validationErrors) > 0) {
            return $this->json([
                'status' => 'error',
                'message' => $validationErrors
            ], 422);
        }

        $dateNow = date('d.m.Y');
        $price = (float) ($params['price'] ?? 0);
        $birthdayString = $params['birthday'] ?? $dateNow;

        // Проверяем формат даты рождения.
        $birthday = \DateTimeImmutable::createFromFormat('d.m.Y', $birthdayString);
        if (!$birthday) {
            return $this->json([
                'status' => 'error',
                'message' => 'Неверный формат даты рождения'
            ], 422);
        }

        $age = $this->getAgeFromString($birthdayString);

        $dateStartWild = $params['dateStartWild'] ?? $dateNow;
        $datePayment = $params['datePayment'] ?? false;

        $totalPrice = 0;

        if ($age < 18) {
            $totalPrice += (new ChildDiscountService(age: $age, basePrice: $price))->calculateDiscount();
        }

        $totalPrice += (new EarlyBookingDiscountService(
            startDate: $dateStartWild,
            paymentDate: $datePayment,
            price: $totalPrice
        ))->calculateEarlyBookingDiscount();

        return $this->json([
            'status' => 'ok',
            'message' => [
                'totalPrice' => $totalPrice,
            ],
        ], 200);
    }

    /**
     * Проверка на обязательные параметры.
     *
     * @param ?array $params
     * @return array
     */
    private function validateParams(?array $params): array
    {
        $errors = [];
        $requireParams = ['price', 'birthday'];

        foreach ($requireParams as $param) {
            if (!isset($params[$param])) {
                $errors[] = "Не передан обязательный параметр: $param";
            }
        }

        return $errors;
    }

    /**
     * Вычислить возраст участника.
     *
     * @param string $birthdayString День рождения участника.
     *
     * @return int Возраст участника.
     */
    private function getAgeFromString(string $birthdayString): int
    {
        $birthday = \DateTimeImmutable::createFromFormat('d.m.Y', $birthdayString);

        $currentDate = new \DateTimeImmutable();
        return $currentDate->diff($birthday)->y;
    }
}
