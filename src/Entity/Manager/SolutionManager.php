<?php
declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Manager;

use App\Entity\Solution;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\Doctrine\Entity\BaseEntityManager;

class SolutionManager extends BaseEntityManager
{

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(Solution::class, $registry);
    }

    /**
     * Returns the latest entries
     * @param bool $isPublic
     * @param int $limit
     * @return mixed
     */
    public function findLatest(bool $isPublic = true, int $limit = 10)
    {
        $queryBuilder = $this->getRepository()
            ->createQueryBuilder('c')
            ->orderby('c.id', 'DESC');
        $parameters = [];
        if ($isPublic) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq($queryBuilder->getRootAliases()[0] . '.isPublished', ':isPublished')
            );
            $queryBuilder->setParameter('isPublished', 1);
        }
        if (!empty($parameters)) {
            $queryBuilder->setParameters($parameters);
        }
        $queryBuilder->setMaxResults($limit);
        return $queryBuilder->getQuery()->getResult();
    }
}