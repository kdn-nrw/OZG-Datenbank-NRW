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

namespace App\Service\Zsm\Api;

use App\Service\Zsm\Api\Model\AbstractDemand;

interface ApiProviderInterface
{
    /**
     * Returns the key for the provider instance
     * @return string
     */
    public function getKey(): string;

    /**
     * Returns the name of the API
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the description for the API
     * @return string
     */
    public function getDescription(): string;

    /**
     * Returns the API demand instance
     * @return AbstractDemand
     */
    public function getDemand(): AbstractDemand;
}
