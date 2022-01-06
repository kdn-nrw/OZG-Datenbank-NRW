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

use App\Import\DataProvider\CsvDataProvider;
use App\Import\ModelRegionProjectImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 */
class ModelRegionProjectImportCommand extends Command
{

    protected static $defaultName = 'app:import:model-region-project';

    /**
     * @var ModelRegionProjectImporter
     */
    private $importer;

    /**
     * @required
     * @param ModelRegionProjectImporter $importer
     */
    public function injectImporter(ModelRegionProjectImporter $importer): void
    {
        $this->importer = $importer;
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Import model region projects from a csv file in a given directory')
            ->addArgument(
                'directory',
                InputArgument::REQUIRED,
                'the import directory'
            )
            ->setHelp('Imports the model region projects from a CSV file in the given directory;'
                . PHP_EOL . 'If you want to get more detailed information, use the --verbose option.');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $directory = $input->getArgument('directory');
        $this->importer->setOutput($output);
        $startTime = microtime(true);
        //$dataProvider = new CsvDataProvider($directory);
        $importedRowCount = -1;//$this->importer->run($dataProvider);
        $durationSeconds = round(microtime(true) - $startTime, 3);
        $io->note(sprintf('Finished import process. %s records were imported in %s seconds', $importedRowCount, $durationSeconds));
    }
}