<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class DefaultController extends AbstractController
{
    #[Route('/', name: 'default_')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Historical Exchange Rates',
            'request_method:' => 'GET',
            'request_url:' => 'api/exchange-rate',
            'request_parameters:' => [
                'base' => 'EUR',
                'target[]' => 'CAD,GBP',
                'at' => '2024-07-04T15:00:01',
            ],
            'example' => 'GET /api/exchange-rate?base=EUR&target[]=CAD&target[]=GBP&&at=2025-07-04T15:00:01'

        ]);
    }
}
