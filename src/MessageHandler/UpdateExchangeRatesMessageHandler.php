<?php

namespace App\MessageHandler;

use App\Entity\ExchangePair;
use App\Service\ExchangeRateManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Message\UpdateExchangeRatesMessage;

#[AsMessageHandler]
final class UpdateExchangeRatesMessageHandler
{
    public function __construct(
        private readonly ExchangeRateManager $exchangeRateManager,
        private readonly EntityManagerInterface $em,
    ) {}

    public function __invoke(UpdateExchangeRatesMessage $message): void
    {
        $pairRepo = $this->em->getRepository(ExchangePair::class);
        /** @var ExchangePair $exchangePair */
        $exchangePair = $pairRepo->find($message->getId());
        $this->exchangeRateManager->updateRates($exchangePair);
    }
}
