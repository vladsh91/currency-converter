<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Converter;
use App\ValueObject\Currency;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:currency:convert', description: 'Convert value to a different currency')]
final class CurrencyConvertCommand extends Command
{
    private const string OPTION_FROM_CURRENCY = 'from-currency';
    private const string OPTION_TO_CURRENCY = 'to-currency';
    private const string ARGUMENT_AMOUNT = 'amount';

    private Currency $fromCurrency;
    private Currency $toCurrency;
    private float $amount;

    private SymfonyStyle $io;

    public function __construct(
        private readonly Converter $converter,
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);

        $fromCurrency = $input->getOption(self::OPTION_FROM_CURRENCY);
        $toCurrency = $input->getOption(self::OPTION_TO_CURRENCY);
        $amount = $input->getArgument(self::ARGUMENT_AMOUNT);

        if (empty($fromCurrency)) {
            throw new \InvalidArgumentException(\sprintf('--%s is not provided.', self::OPTION_FROM_CURRENCY));
        }

        if (empty($toCurrency)) {
            throw new \InvalidArgumentException(\sprintf('--%s is not provided.', self::OPTION_TO_CURRENCY));
        }

        if (empty($amount) || !\is_numeric($amount)) {
            throw new \InvalidArgumentException(
                \sprintf('<%s> is not provided or invalid.', self::ARGUMENT_AMOUNT),
            );
        }

        $this->fromCurrency = Currency::fromString($fromCurrency);
        $this->toCurrency = Currency::fromString($toCurrency);
        $this->amount = (float) $amount;
    }

    protected function configure(): void
    {
        $this->addOption(self::OPTION_FROM_CURRENCY, null, InputOption::VALUE_REQUIRED);
        $this->addOption(self::OPTION_TO_CURRENCY, null, InputOption::VALUE_REQUIRED);
        $this->addArgument(self::ARGUMENT_AMOUNT, InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $convertedAmount = $this->converter->convert(
            $this->fromCurrency,
            $this->toCurrency,
            $this->amount,
        );

        if ($convertedAmount === null) {
            $this->io->error(\sprintf(
                'Cannot convert value because %s/%s rate is not available.',
                $this->fromCurrency->value,
                $this->toCurrency->value,
            ));

            return Command::FAILURE;
        }

        $this->io->writeln(\json_encode([
            'amount' => $convertedAmount->amount,
            'currency_from' => [
                'rate' => $convertedAmount->rate->inverseRate,
                'code' => $convertedAmount->rate->fromCurrency->value,
            ],
            'currency_to' => [
                'rate' => 1,
                'code' => $convertedAmount->rate->toCurrency->value,
            ],
        ], flags: JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
