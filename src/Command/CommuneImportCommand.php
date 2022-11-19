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

namespace App\Command;

use App\Import\CommuneImporter;
use App\Import\DataProvider\CsvDataProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 */
class CommuneImportCommand extends Command
{

    protected static $defaultName = 'app:import:commune';

    /**
     * @var CommuneImporter
     */
    private $importer;

    /**
     * @required
     * @param CommuneImporter $importer
     */
    public function injectImporter(CommuneImporter $importer): void
    {
        $this->importer = $importer;
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Import communes from a csv file in a given directory')
            ->addArgument(
                'directory',
                InputArgument::REQUIRED,
                'the import directory'
            )
            ->setHelp('Imports the communes from a CSV file in the given directory;'
                . PHP_EOL . 'If you want to get more detailed information, use the --verbose option.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $directory = $input->getArgument('directory');
        $startTime = microtime(true);
        $this->importer->setOutput($output);
        $dataProvider = new CsvDataProvider($directory);
        $importedRowCount = $this->importer->run($dataProvider);
        $durationSeconds = round(microtime(true) - $startTime, 3);
        $io->note(sprintf('Finished import process. %s records were imported in %s seconds', $importedRowCount, $durationSeconds));
        return 0;
    }
}