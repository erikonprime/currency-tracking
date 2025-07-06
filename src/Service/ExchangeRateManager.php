<?php

namespace App\Service;

use App\Dto\ExchangeRateDTO;
use App\Dto\RateCollectionDTO;
use App\Dto\RatesDTO;
use App\Entity\ExchangePair;
use App\Entity\ExchangeRate;
use App\Repository\ExchangePairRepository;
use Doctrine\ORM\EntityManagerInterface;
use FreeCurrencyApi\FreeCurrencyApi\FreeCurrencyApiClient;

class ExchangeRateManager
{
    public function __construct(
        private FreeCurrencyApiClient $currencyApiClient,
        private readonly EntityManagerInterface $em,
        private readonly ExchangePairRepository $exchangePairRepository,
    ) {}

    public function updateRates(ExchangePair $exchangePair): void
    {
        $res = $this->currencyApiClient->latest([
            'base_currency' => $exchangePair->getBaseCurrency(),
            'currencies' => $exchangePair->getTargetCurrency(),
        ]);

        $rate = $res['data'][$exchangePair->getTargetCurrency()] ?? null;
        if (!$rate) {
            throw new \RuntimeException();
        }

        $rate = $res['data'][$exchangePair->getTargetCurrency()] ?? null;

        $rate = new ExchangeRate($exchangePair, $rate);

        $this->em->persist($rate);
        $this->em->flush();
    }

    public function getRates(ExchangeRateDTO $exchangeRateDTO): RateCollectionDTO
    {
        $pairs = $this->exchangePairRepository->getExchangePairRatesByTime($exchangeRateDTO);

        $data = [];
        /** @var ExchangePair $pair */
        foreach ($pairs as $pair) {
            /** @var ExchangeRate $exchangeRate */
            foreach ($pair->getExchangeRates() as $exchangeRate) {
                $data[] = new RatesDTO(
                    $pair->getBaseCurrency(),
                    $pair->getTargetCurrency(),
                    $exchangeRate->getRate(),
                    $exchangeRate->getCreatedAt()->format('Y-m-d H:i:s'),
                );
            }
        }

        return new RateCollectionDTO($data);
    }
}
