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

namespace App\Service;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\PageContent;

class PageContentManager
{
    use InjectManagerRegistryTrait;

    /**
     * Returns an array with the contents for the given page key
     *
     * @param int $pageKey
     * @return array
     */
    public function getPageContent(int $pageKey): array
    {
        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->registry->getRepository(PageContent::class);
        return $repository->findBy(['page' => $pageKey], ['position' => 'ASC', 'id' => 'ASC']);
    }
}