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

use App\Search\Indexer;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("*\/15 * * * *")
 * Will be executed every 5 minutes
 */
class SearchIndexCommand extends Command
{
    protected static $defaultName = 'app:search:index';
    /**
     * @var Indexer
     */
    private $indexer;

    /**
     * @param Indexer $indexer
     */
    public function __construct(Indexer $indexer)
    {
        parent::__construct();
        $this->indexer = $indexer;
    }

    public function configure(): void
    {
        $this
            ->setDescription('Run search indexer for entities')
            ->addOption(
                'limit',
                'l',
                InputOption::VALUE_REQUIRED,
                'Limit number of indexed records',
                100
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $limit = max(1, min(5000, (int)$input->getOption('limit')));
        $changedRecordCount = $this->indexer->run($limit);
        $io->note(sprintf('Finished indexing. %s records were changed', $changedRecordCount));
    }
}