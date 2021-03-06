<?php

declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Api\Consumer\Command;

use App\Api\Consumer\ApiManager;
use App\Api\Consumer\InjectApiManagerTrait;
use App\Api\Consumer\ZuFiConsumer;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("*\/23 * * * *")
 * Will be executed every 23 minutes
 */
class ZuFiCommuneImportCommand extends Command
{
    use InjectApiManagerTrait;

    protected static $defaultName = 'app:api:consumer:zufi-communes';

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Import ZuFi commune service data from the API')
            ->addArgument(
                'serviceKeys',
                InputArgument::OPTIONAL,
                'optional comma separated list of service keys for import'
            )
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_OPTIONAL,
                'the maximum number of updated rows',
                200
            )
            ->addOption(
                'sorting',
                's',
                InputOption::VALUE_OPTIONAL,
                'sort order of communes to be updated',
                'random'
            )
            ->setHelp('Imports the commune service data from the ZuFi API;'
                . PHP_EOL . 'If you want to get more detailed information, use the --verbose option.');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $limit = (int)$input->getOption('limit');
        $serviceKeys = array_filter(explode(',', (string)$input->getArgument('serviceKeys')));
        if (!empty($serviceKeys)) {
            $io->note(sprintf('Starting import process. Limiting imported items to services %s', implode(',', $serviceKeys)));
        }
        $startTime = microtime(true);
        $consumer = $this->apiManager->getConfiguredConsumer(ApiManager::API_KEY_ZU_FI);
        /** @var ZuFiConsumer $consumer */
        $consumer->setOutput($output);
        $sorting = (string)$input->getOption('sorting');
        $importedRowCount = $consumer->importCommuneServiceResults($limit, $serviceKeys, $sorting);
        $durationSeconds = round(microtime(true) - $startTime, 3);
        $io->note(sprintf('Finished import process. %s records were imported in %s seconds', $importedRowCount, $durationSeconds));
    }
}