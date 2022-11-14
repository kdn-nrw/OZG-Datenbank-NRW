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

use App\Service\ImplementationProjectHelper;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("17 1 * * *")
 * Will be executed every day at 01:17
 */
class ImplementationProjectStatusCommand extends Command
{
    protected static $defaultName = 'app:implementation-project:status';
    /**
     * @var ImplementationProjectHelper
     */
    private $implementationProjectHelper;

    /**
     * @required
     * @param ImplementationProjectHelper $implementationProjectHelper
     */
    public function injectImplementationProjectHelper(ImplementationProjectHelper $implementationProjectHelper): void
    {
        $this->implementationProjectHelper = $implementationProjectHelper;
    }

    public function configure(): void
    {
        $this
            ->setDescription('Update the implementation project status based on the given dates')
            ->addOption(
                'force-update-status-id',
                'f',
                InputOption::VALUE_OPTIONAL,
                'force update check for all projects with given status id'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $startTime = microtime(true);
        $forceUpdateStatusId = (int) $input->getOption('force-update-status-id');
        $updatedRowCount = $this->implementationProjectHelper->setCurrentStatusForAll($forceUpdateStatusId);
        $durationSeconds = round(microtime(true) - $startTime, 3);
        $io->note(sprintf('Finished update process. %s records were update in %s seconds', $updatedRowCount, $durationSeconds));
    }
}