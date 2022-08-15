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
use App\Entity\Onboarding\Epayment;
use App\Entity\Onboarding\FormSolution;
use App\Entity\Onboarding\MonumentAuthority;
use App\Entity\Onboarding\Release;
use App\Entity\Onboarding\ServiceAccount;
use App\Entity\Onboarding\XtaServer;
use App\Service\Onboarding\InjectOnboardingManagerTrait;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("47 6,11,14,17 * * 1-5")
 * Will be executed mo-fr at 06:47, 11:47, 14:47 and 17:47
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
        $importedRowCount += $this->onboardingManager->createItems(Epayment::class);
        $this->onboardingManager->updateAllOnboardingSolutions();
        $importedRowCount += $this->onboardingManager->createItems(FormSolution::class);
        $importedRowCount += $this->onboardingManager->createItems(Release::class);
        $importedRowCount += $this->onboardingManager->createItems(ServiceAccount::class);
        $importedRowCount += $this->onboardingManager->createItems(XtaServer::class);
        $importedRowCount += $this->onboardingManager->createItems(MonumentAuthority::class);
        $durationSeconds = round(microtime(true) - $startTime, 3);
        $io->note(sprintf('Finished onboarding update process. %s records were imported in %s seconds', $importedRowCount, $durationSeconds));
    }
}