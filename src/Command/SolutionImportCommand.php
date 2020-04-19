<?php

declare(strict_types=1);

namespace App\Command;

use App\Import\SolutionImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 */
class SolutionImportCommand extends Command
{

    protected static $defaultName = 'app:import:solution';

    /**
     * @var SolutionImporter
     */
    private $importer;

    /**
     * @param SolutionImporter $importer
     */
    public function __construct(SolutionImporter $importer)
    {
        parent::__construct();
        $this->importer = $importer;
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Import solutions from a csv file in a given directory')
            ->addArgument(
                'directory',
                InputArgument::REQUIRED,
                'the import directory'
            )
            ->addArgument(
                'form-server-id',
                InputArgument::REQUIRED,
                'the form server id'
            )
            ->setHelp('Imports the solutions from a CSV file in the given directory;'
                . PHP_EOL . 'If you want to get more detailed information, use the --verbose option.');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $directory = $input->getArgument('directory');
        $formServerId = (int)$input->getArgument('form-server-id');
        $this->importer->setOutput($output);
        $this->importer->run($directory, $formServerId);
    }
}