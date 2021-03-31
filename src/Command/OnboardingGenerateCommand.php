<?php

declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\Entity\Onboarding\CommuneInfo;
use App\Entity\Onboarding\Dataclearing;
use App\Entity\Onboarding\Epayment;
use App\Entity\Onboarding\ServiceAccount;
use App\Service\Onboarding\InjectOnboardingManagerTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 */
class OnboardingGenerateCommand extends Command
{
    use InjectOnboardingManagerTrait;

    protected static $defaultName = 'app:onboarding:generate';

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Generate onboarding entities from communes');
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $startTime = microtime(true);
        $importedRowCount = 0;
        $importedRowCount += $this->onboardingManager->createItems(CommuneInfo::class);
        $importedRowCount += $this->onboardingManager->createItems(Dataclearing::class);
        $importedRowCount += $this->onboardingManager->createItems(Epayment::class);
        $importedRowCount += $this->onboardingManager->createItems(ServiceAccount::class);
        $durationSeconds = round(microtime(true) - $startTime, 3);
        $io->note(sprintf('Finished import process. %s records were imported in %s seconds', $importedRowCount, $durationSeconds));
    }
}