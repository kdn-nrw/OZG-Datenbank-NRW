<?php
declare(strict_types=1);

namespace App\Entity;

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

    public function getPager(array $criteria, $page, $limit = 10, array $sort = [])
    {
        if (!isset($criteria['mode'])) {
            $criteria['mode'] = 'public';
        }
        $parameters = [];
        $query = $this->getRepository()
            ->createQueryBuilder('c')
            ->orderby('c.id', 'DESC');
//        if ('public' === $criteria['mode']) {
//            $criteria['status'] = $criteria['status'] ?? CommentInterface::STATUS_VALID;
//            $query->andWhere('c.status = :status');
//            $parameters['status'] = $criteria['status'];
//        }
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