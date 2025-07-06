<?php

namespace App\Controller;

use App\Dto\ExchangeRateDTO;
use App\Service\ExchangeRateManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use DateTimeImmutable;

final class ExchangeRatesController extends AbstractController
{
    #[Route('/api/exchange-rate', name: 'exchange-rate')]
    public function getExchangeRate(Request $request, ExchangeRateManager $exchangeRateManager): JsonResponse
    {
        $datetimeStr = $request->query->get('at');
        $base = $request->query->get('base');
        $targets = $request->query->all('target');

        try {
            $rateHistoricalDTO = new ExchangeRateDTO($base, $targets, new DateTimeImmutable($datetimeStr));

            $rates = $exchangeRateManager->getRates($rateHistoricalDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }

        return $this->json($rates);
    }
}
