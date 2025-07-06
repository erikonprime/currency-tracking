<?php

namespace App\Repository;

use App\Dto\ExchangeRateDTO;
use App\Dto\RateHistoricalDTO;
use App\Entity\ExchangePair;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Parameter;

/**
 * @extends ServiceEntityRepository<ExchangePair>
 */
class ExchangePairRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangePair::class);
    }

    public function getExchangePairRates(RateHistoricalDTO $dto): ?ExchangePair
    {
        return $this
            ->createQueryBuilder('e')
            ->addSelect('p')
            ->innerJoin('e.exchangeRates', 'p')
            ->andWhere('e.baseCurrency = :base')
            ->andWhere('e.targetCurrency = :target')
            ->setParameters(
                new ArrayCollection(
                    [
                        new Parameter('base', $dto->base),
                        new Parameter('target', $dto->target),
                    ],
                ),
            )
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getExchangePairRatesByTime(ExchangeRateDTO $dto): array
    {
        $start = $dto->date;
        $end = $dto->date->modify('+1 second');

        return $this
            ->createQueryBuilder('e')
            ->addSelect('p')
            ->innerJoin('e.exchangeRates', 'p')
            ->andWhere('e.baseCurrency = :base')
            ->andWhere('e.targetCurrency IN (:targets)')
            ->andWhere('p.createdAt BETWEEN :startDt AND :endDt')
            ->setParameters(
                new ArrayCollection(
                    [
                        new Parameter('base', $dto->base),
                        new Parameter('targets', $dto->targets),
                        new Parameter('startDt', $start),
                        new Parameter('endDt', $end),
                    ],
                ),
            )
            ->getQuery()
            ->getResult();
    }
}
