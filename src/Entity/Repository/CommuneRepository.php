<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Repository;

use App\Entity\StateGroup\Commune;
use Doctrine\ORM\EntityRepository;

class CommuneRepository extends EntityRepository
{

    /**
     * @param string $orderBy
     * @param string $orderDirection
     * @return Commune[]|mixed
     */
    public function findAllWithMissingKeys(string $orderBy = 'id', string $orderDirection = 'ASC')
    {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder
            ->where('c.officialCommunityKey IS NULL OR c.regionalKey IS NULL');
        $queryBuilder->orderBy('c.' . $orderBy, $orderDirection === 'DESC' ? 'DESC' : 'ASC');
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }
}
