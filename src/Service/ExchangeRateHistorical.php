<?php

namespace App\Service;

use App\Dto\RateHistoricalDTO;
use App\Dto\RateHistoricalCollectionDTO;
use App\Entity\ExchangeRate;
use App\Repository\ExchangePairRepository;

class ExchangeRateHistorical
{
    public function __construct(
        private readonly ExchangePairRepository $exchangePairRepository,
    ) {}

    public function getRates(RateHistoricalDTO $dto): RateHistoricalCollectionDTO
    {
        $pair = $this->exchangePairRepository->getExchangePairRates($dto);

        if (!$pair) {
            return new RateHistoricalCollectionDTO();
        }
        $data = [];
        /** @var ExchangeRate $rate */
        foreach ($pair->getExchangeRates() as $rate) {
            $data[] = new RateHistoricalDTO(
                $dto->base,
                $dto->target,
                $rate->getRate(),
                $rate->getCreatedAt(),
            );
        }

        return new RateHistoricalCollectionDTO($data);
    }
}
