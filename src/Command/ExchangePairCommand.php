<?php

namespace App\Command;

use App\Dto\RateHistoricalDTO;
use App\Entity\ExchangePair;
use App\Service\ExchangeRateHistorical;
use Doctrine\ORM\EntityManagerInterface;
use FreeCurrencyApi\FreeCurrencyApi\FreeCurrencyApiClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'exchange:pair',
    description: 'Command to add/remove currency pairs',
)]
class ExchangePairCommand extends Command
{
    private const ACTION_ADD = 'add';
    private const ACTION_REMOVE = 'remove';
    private const ACTION_HISTORY = 'history';

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly FreeCurrencyApiClient $currencyApiClient,
        private readonly ValidatorInterface $validator,
        private readonly ExchangeRateHistorical $rateHistorical,
    ) {
        parent::__construct();
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $helper = new QuestionHelper();

        $baseCurrencyQuestion = new Question('Please enter the base currency: ');
        $targetCurrencyQuestion = new Question('Please enter the target currency: ');
        $actionQuestion = new ChoiceQuestion(
            'Please select action ("add" by default)',
            [self::ACTION_ADD, self::ACTION_REMOVE, self::ACTION_HISTORY],
            0,
        );

        $validateCurrency = fn($value) => strtoupper(trim($value)) ?: throw new \RuntimeException(
            'Currency is required.',
        );

        $baseCurrencyQuestion->setValidator($validateCurrency);
        $targetCurrencyQuestion->setValidator($validateCurrency);

        $base = $helper->ask($input, $output, $baseCurrencyQuestion);
        $target = $helper->ask($input, $output, $targetCurrencyQuestion);
        $action = $helper->ask($input, $output, $actionQuestion);

        return match ($action) {
            self::ACTION_REMOVE => $this->handleRemove($base, $target, $io),
            self::ACTION_HISTORY => $this->handleHistory($base, $target, $io),
            self::ACTION_ADD => $this->handleAdd($base, $target, $io),
            default => throw new \Exception('Unsupported action: ' . $action),
        };
    }

    private function handleAdd(string $base, string $target, SymfonyStyle $io): int
    {
        $res = $this->currencyApiClient->currencies([
            'currencies' => sprintf('%s,%s', $base, $target),
        ]);

        if (!isset($res['data'])) {
            $io->error(sprintf('Cannot add  pair "%s" and "%s"', $base, $target));
            $io->error('The selected currencies is invalid');

            return Command::SUCCESS;
        }

        $exchangePair = new ExchangePair($base, $target);

        $errors = $this->validator->validate($exchangePair);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $io->error($error->getMessage());
            }

            return Command::SUCCESS;
        }

        $this->em->persist($exchangePair);
        $this->em->flush();

        $io->success(sprintf('Exchange pair "%s"  and "%s" has been added', $base, $target));

        return Command::SUCCESS;
    }

    private function handleRemove(string $base, string $target, SymfonyStyle $io): int
    {
        $pairRepo = $this->em->getRepository(ExchangePair::class);
        /** @var ExchangePair $exchangePair */
        $exchangePair = $pairRepo->findOneBy([
            'baseCurrency' => $base,
            'targetCurrency' => $target,
        ]);
        if (!$exchangePair) {
            $io->note(sprintf('Exchange pair "%s"  and "%s" not found', $base, $target));;

            return Command::SUCCESS;
        }
        $this->em->remove($exchangePair);
        $this->em->flush();
        $io->success(sprintf('Exchange pair "%s"  and "%s" was removed', $base, $target));

        return Command::SUCCESS;
    }

    private function handleHistory(string $base, string $target, SymfonyStyle $io): int
    {
        $ratesDto = new RateHistoricalDTO($base, $target);

        $collectionDto = $this->rateHistorical->getRates($ratesDto);

        $io->table(
            ['Base Currency', 'Target Currency', 'Rate', 'Date Time'],
            array_map(function (RateHistoricalDTO $rate) {
                return [
                    'base' => $rate->base,
                    'target' => $rate->target,
                    'rate' => $rate->rate,
                    'DateTime' => $rate->date->format('Y-m-d H:i:s'),
                ];
            }, $collectionDto->items),
        );

        return Command::SUCCESS;
    }
}
