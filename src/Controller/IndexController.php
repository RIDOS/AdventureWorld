<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index_app')]
    public function index(): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
            'message' => 'Перейди к маршруту: /api/calculate_travel_with_discount',
        ], 200);
    }
}