<?php

namespace App\Command;

use App\Entity\ExchangePair;
use App\Message\UpdateExchangeRatesMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand(
    name: 'exchange:pair:track',
    description: 'Command to track currency pairs',
)]
#[AsCronTask('* * * * *')]
class ExchangePairTrackCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $pairs = $this->em->getRepository(ExchangePair::class)->findAll();
        /** @var ExchangePair $pair */
        foreach ($pairs as $pair) {
            $this->bus->dispatch(new UpdateExchangeRatesMessage($pair->getId()));
            $io->success(
                sprintf('Dispatched %s -> %s exchange rates', $pair->getBaseCurrency(), $pair->getTargetCurrency()),
            );
        }

        return Command::SUCCESS;
    }
}
