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

namespace App\Service\MetaData;

trait InjectMetaDataManagerTrait
{
    /**
     * @var MetaDataManager
     */
    protected $metaDataManager;

    /**
     * @required
     * @param MetaDataManager $metaDataManager
     */
    public function injectMetaDataManager(MetaDataManager $metaDataManager): void
    {
        $this->metaDataManager = $metaDataManager;
    }
}