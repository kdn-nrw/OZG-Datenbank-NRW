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

namespace App\Twig\Extension;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\PageContent;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RenderPageContentExtension extends AbstractExtension
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
            new TwigFunction('app_get_page_content', [$this, 'getPageContent']),
        ];
    }

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
