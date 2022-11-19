<?php

declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\ModelRegion\ModelRegionProject;
use Doctrine\ORM\EntityRepository;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("19 1 * * *")
 * Will be executed every day at 01:19
 */
class ModelRegionProjectStatusCommand extends Command
{
    protected static $defaultName = 'app:model-region-project:status';

    use InjectManagerRegistryTrait;

    public function configure(): void
    {
        $this
            ->setDescription('Update the model region project status based on the given project dates');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());
        $startTime = microtime(true);

        $em = $this->getEntityManager();
        /** @var EntityRepository $repository */
        $repository = $em->getRepository(ModelRegionProject::class);
        $queryBuilder = $repository->createQueryBuilder('s');
        $query = $queryBuilder->getQuery();
        $result = $query->execute();
        $updatedRowCount = 0;
        $now = date_create();
        $now->setTimezone(new \DateTimeZone('UTC'));
        foreach ($result as $entity) {
            /** @var ModelRegionProject $entity */
            $status = $entity->determineNewStatus();
            if ($status !== $entity->getStatus()) {
                $entity->setStatus($status);
                ++$updatedRowCount;
            }
        }
        if ($updatedRowCount > 0) {
            $em->flush();
        }
        $durationSeconds = round(microtime(true) - $startTime, 3);
        $io->note(sprintf('Finished update process. %s records were update in %s seconds', $updatedRowCount, $durationSeconds));
        return 0;
    }
}