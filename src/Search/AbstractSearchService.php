<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Search;

use App\Entity\Repository\SearchIndexRepository;
use App\Entity\SearchIndexWord;
use App\Service\InjectApplicationContextHandlerTrait;
use Doctrine\Persistence\ManagerRegistry;

/**
 * AbstractSearchService
 */
abstract class AbstractSearchService
{

    use InjectApplicationContextHandlerTrait;

    /**
     * @var \Doctrine\Persistence\ManagerRegistry|ManagerRegistry
     */
    protected $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return SearchIndexRepository
     */
    protected function getIndexRepository(): SearchIndexRepository
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->registry->getManager();
        return $em->getRepository(SearchIndexWord::class);
    }
}