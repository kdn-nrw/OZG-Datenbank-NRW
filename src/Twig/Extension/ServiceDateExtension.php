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

use App\Entity\Service;
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
            new TwigFunction('app_get_service_date', [$this, 'getServiceDate']),
        ];
    }

    /**
     * Returns the field description collection for the referenced fields
     *
     * @param Service $service
     * @return string
     */
    public function getFormattedServiceDate(Service $service): string
    {
        if ($serviceCreatedAt = $this->getServiceDate($service)) {
            return date('d.m.Y', $serviceCreatedAt->getTimestamp());
        }
        return '';
    }

    /**
     * Returns the field description collection for the referenced fields
     *
     * @param Service $service
     * @return ?\DateTime
     */
    public function getServiceDate(Service $service): ?\DateTime
    {
        $serviceBaseResult = $service->getServiceBaseResult();
        if (null !== $serviceBaseResult) {
            $serviceCreatedAt = $serviceBaseResult->getServiceCreatedAt();
            if (null === $serviceCreatedAt) {
                $serviceCreatedAt = $serviceBaseResult->getConvertedDate();
            }
            return $serviceCreatedAt;
        }
        return null;
    }
}
