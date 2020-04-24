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
use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
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

    public function getPager(array $criteria, $page, $limit = 10, array $sort = []): Pager
    {
        if (!isset($criteria['mode'])) {
            $criteria['mode'] = 'public';
        }
        $parameters = [];
        $query = $this->getRepository()
            ->createQueryBuilder('c')
            ->orderby('c.id', 'DESC');
        if ('public' === $criteria['mode']) {
            /** @var \Doctrine\ORM\QueryBuilder $query */
            $query->andWhere(
                $query->expr()->eq($query->getRootAliases()[0] . '.isPublished', ':isPublished')
            );
            $query->setParameter('isPublished', 1);
        }
        if (!empty($parameters)) {
            $query->setParameters($parameters);
        }
        $pager = new Pager();
        $pager->setMaxPerPage($limit);
        $pager->setQuery(new ProxyQuery($query));
        $pager->setPage($page);
        $pager->init();
        return $pager;
    }
}