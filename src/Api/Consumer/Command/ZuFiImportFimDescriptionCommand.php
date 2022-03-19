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
use App\Api\Consumer\Model\ZuFiDemand;
use App\Api\Consumer\ZuFiConsumer;
use App\Entity\FederalInformationManagementType;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("*\/17 * * * *")
 * Will be executed every 17 minutes
 */
class ZuFiImportFimDescriptionCommand extends Command
{
    use InjectApiManagerTrait;

    protected static $defaultName = 'app:api:consumer:zufi-fim-desc';

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Import ZuFi service data for the FIM descriptions from the API')
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
                100
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_OPTIONAL,
                'force update of rows',
                false
            )
            ->setHelp('Imports the service data from the ZuFi API;'
                . PHP_EOL . 'If you want to get more detailed information, use the --verbose option.');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $regionalKey = \App\Api\Consumer\ZuFiConsumer::DEFAULT_REGIONAL_KEY;
        $fimType = FederalInformationManagementType::TYPE_DESCRIPTION;
        $serviceKeys = array_filter(explode(',', (string) $input->getArgument('serviceKeys')));
        if (!empty($serviceKeys)) {
            $io->note(sprintf('Starting import process. Limiting imported items to services %s', implode(',', $serviceKeys)));
        }
        $limit = (int) $input->getOption('limit');
        $force = (bool)$input->getOption('force');
        $startTime = microtime(true);
        $consumer = $this->apiManager->getConfiguredConsumer(ApiManager::API_KEY_ZU_FI);
        /** @var ZuFiConsumer $consumer */
        $consumer->setOutput($output);
        $demand = $consumer->getDemand();
        /** @var ZuFiDemand $demand */
        $demand->setRegionalKey($regionalKey);
        $importedRowCount = $consumer->importServiceResults($limit, $fimType, null, $serviceKeys, $force);
        $durationSeconds = round(microtime(true) - $startTime, 3);
        $io->note(sprintf('Finished import process. %s records were imported in %s seconds', $importedRowCount, $durationSeconds));
    }
}