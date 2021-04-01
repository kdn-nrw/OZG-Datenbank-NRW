<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig\Extension;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Service;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LastUpdateExtension extends AbstractExtension
{
    use InjectManagerRegistryTrait;

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('app_last_update', [$this, 'getApplicationLastUpdate']),
        ];
    }

    /**
     * Returns the date of the last update
     *
     * @return DateTime
     */
    public function getApplicationLastUpdate(): DateTime
    {
        $em = $this->getEntityManager();
        $repository = $em->getRepository(Service::class);
        $queryBuilder = $repository->createQueryBuilder('s');

        $query = $queryBuilder->select('MAX(s.modifiedAt)')
            ->where('s.hidden = 0')
            ->setMaxResults(1)
            ->getQuery();
        $lastUpdate = null;
        try {
            $result = $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            $result = null;
        }
        if (null !== $result) {
            $lastUpdate = date_create(current($result));
        } else {
            $lastUpdate = date_create();
        }
        return $lastUpdate;
    }
}
