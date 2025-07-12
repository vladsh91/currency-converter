<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\RateQuery\RateQuery;
use App\ValueObject\Currency;
use App\ValueObject\Rate;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:rate:show')]
final class RateShowCommand extends Command
{
    private const string OPTION_CURRENCY = 'currency';
    private const string DEFAULT_CURRENCY = 'USD';

    private Currency $currency;

    public function __construct(
        private readonly RateQuery $rateQuery,
    ) {
        parent::__construct();
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->currency = Currency::fromString($input->getOption(self::OPTION_CURRENCY) ?? self::DEFAULT_CURRENCY);
    }

    protected function configure(): void
    {
        $this->addOption(self::OPTION_CURRENCY, null, InputOption::VALUE_REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $rates = \array_map(
            static fn (Rate $rate) => [
                'code' => $rate->toCurrency->value,
                'rate' => $rate->rate,
            ],
            $this->rateQuery->all($this->currency),
        );

        echo \json_encode(\array_values($rates), flags: JSON_PRETTY_PRINT) . PHP_EOL;

        return Command::SUCCESS;
    }
}
