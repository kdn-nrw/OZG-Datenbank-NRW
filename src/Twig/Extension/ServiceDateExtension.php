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

use App\Entity\Api\ServiceBaseResult;
use App\Entity\Service;
use App\Entity\StateGroup\Commune;
use Twig\TwigFunction;

class ServiceDateExtension extends ApiQueryExtension
{
    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('app_format_service_date', [$this, 'getFormattedServiceDate']),
        ];
    }

    /**
     * Returns the field description collection for the referenced fields
     *
     * @param Commune|Service $parent
     * @param Service|null $service
     * @return string
     */
    public function getFormattedServiceDate($parent, Service $service = null): string
    {
        $serviceBaseResult = $this->getServiceBaseResult($parent, $service);
        if (null !== $serviceBaseResult) {
            $serviceCreatedAt = $serviceBaseResult->getServiceCreatedAt();
            if (null === $serviceCreatedAt) {
                $serviceCreatedAt = $serviceBaseResult->getConvertedDate();
            }
            if (null !== $serviceCreatedAt) {
                return date('d.m.Y', $serviceCreatedAt->getTimestamp());
            }
        }
        return '';
    }
}
