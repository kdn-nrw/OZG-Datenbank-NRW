<?php

declare(strict_types=1);

namespace App\Command;

use App\Import\CmsContactImport;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @CronJob("*\/5 * * * *")
 * Will be executed every 5 minutes
 */
class ContactImportCommand extends Command
{
    protected static $defaultName = 'app:import:contact';
    /**
     * @var CmsContactImport
     */
    private $cmsContactImport;

    /**
     * @param CmsContactImport $cmsContactImport
     */
    public function __construct(CmsContactImport $cmsContactImport)
    {
        parent::__construct();
        $this->cmsContactImport = $cmsContactImport;
    }

    public function configure(): void
    {
        $this
            ->setDescription('Import contacts from defined data sources');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->cmsContactImport->import();
    }
}