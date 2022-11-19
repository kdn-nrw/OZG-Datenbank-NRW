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
use App\Api\Consumer\ArsAgsConsumer;
use App\Api\Consumer\InjectApiManagerTrait;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("17 1 * * 7")
 * Will be executed every sunday at 1:17
 */
class ArsAgsImportCommand extends Command
{
    use InjectApiManagerTrait;

    protected static $defaultName = 'app:api:consumer:ars-ags';

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Import ARS/AGS commune data from the API')
            ->addArgument(
                'limit',
                InputArgument::OPTIONAL,
                'the maximum number of updated rows',
                500
            )
            ->setHelp('Imports the service data from the ARS/AGS API;'
                . PHP_EOL . 'If you want to get more detailed information, use the --verbose option.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $limit = (int)$input->getArgument('limit');
        $startTime = microtime(true);
        $consumer = $this->apiManager->getConfiguredConsumer(ApiManager::API_KEY_ARS_AGS);
        if ($consumer instanceof ArsAgsConsumer) {
            $consumer->setOutput($output);
            $importedRowCount = $consumer->importServiceResults($limit);
            $durationSeconds = round(microtime(true) - $startTime, 3);
            $io->note(sprintf('Finished import process. %s records were imported in %s seconds', $importedRowCount, $durationSeconds));
            return 0;
        }
        return 1;
    }
}