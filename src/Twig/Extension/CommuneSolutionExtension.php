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
use App\Entity\Solution;
use App\Entity\StateGroup\Commune;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CommuneSolutionExtension extends AbstractExtension
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
            new TwigFunction('commune_solutions', [$this, 'getCommuneSolutions']),
        ];
    }

    /**
     * Returns an array with the contents for the given page key
     *
     * @param Commune $commune
     * @param bool $showUnpublishedSolutions
     * @return Collection
     */
    public function getCommuneSolutions(Commune $commune, bool $showUnpublishedSolutions = false): Collection
    {
        $entities = new ArrayCollection();
        foreach ($commune->getSolutions() as $solution) {
            if (!$solution->isHidden() && ($showUnpublishedSolutions || $solution->isPublished())) {
                $entities->add($solution);
            }
        }
        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->registry->getRepository(Solution::class);
        $generalSolutions = $repository->findBy(['communeType' => Solution::COMMUNE_TYPE_ALL]);
        foreach ($generalSolutions as $solution) {
            if (!$entities->contains($solution) && !$solution->isHidden()
                && ($showUnpublishedSolutions || $solution->isPublished())) {
                $entities->add($solution);
            }
        }
        return $entities;
    }
}
