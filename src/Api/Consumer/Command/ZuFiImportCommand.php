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
use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\StateGroup\Commune;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 */
class ZuFiImportCommand extends Command
{
    use InjectApiManagerTrait;
    use InjectManagerRegistryTrait;


    protected static $defaultName = 'app:api:consumer:zufi';

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Import ZuFi service data from the API')
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
            ->addArgument(
                'regional-key',
                'r',
                InputOption::VALUE_OPTIONAL,
                'the regional key'
            )
            ->addOption(
                'commune-id',
                'c',
                InputOption::VALUE_OPTIONAL,
                'optional commune id'
            )
            ->setHelp('Imports the service data from the ZuFi API;'
                . PHP_EOL . 'If you want to get more detailed information, use the --verbose option.');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $regionalKey = (string) $input->getOption('regionalKey');
        $limit = (int) $input->getOption('limit');
        $startTime = microtime(true);
        $consumer = $this->apiManager->getConfiguredConsumer(ApiManager::API_KEY_ZU_FI);
        /** @var ZuFiConsumer $consumer */
        $consumer->setOutput($output);
        /** @var ZuFiDemand $demand */
        $demand = $consumer->getDemand();
        $commune = null;
        if (0 < $communeId = (int) $input->getOption('commune-id')) {
            $commune = $this->getEntityManager()->find(Commune::class, $communeId);
        } elseif ($regionalKey) {
            $demand->setRegionalKey($regionalKey);
            if ($regionalKey !== ZuFiConsumer::DEFAULT_REGIONAL_KEY) {
                $repository = $this->getEntityManager()->getRepository(Commune::class);
                $commune = $repository->findOneBy(['regionalKey' => $regionalKey]);
            }
        }
        $serviceKeys = array_filter(explode(',', (string) $input->getArgument('serviceKeys')));
        if (!empty($serviceKeys)) {
            $io->note(sprintf('Starting import process. Limiting imported items to services %s', implode(',', $serviceKeys)));
        }
        $importedRowCount = $consumer->importServiceResults($limit, $commune, $serviceKeys);
        $durationSeconds = round(microtime(true) - $startTime, 3);
        $io->note(sprintf('Finished import process. %s records were imported in %s seconds', $importedRowCount, $durationSeconds));
    }
}