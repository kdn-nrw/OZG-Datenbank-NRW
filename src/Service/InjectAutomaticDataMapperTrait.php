<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

trait InjectAutomaticDataMapperTrait
{

    /**
     * @var AutomaticDataMapper
     */
    protected $automaticDataMapper;

    /**
     * @required
     * @param AutomaticDataMapper $automaticDataMapper
     */
    public function injectAutomaticDataMapper(AutomaticDataMapper $automaticDataMapper): void
    {
        $this->automaticDataMapper = $automaticDataMapper;
    }

}