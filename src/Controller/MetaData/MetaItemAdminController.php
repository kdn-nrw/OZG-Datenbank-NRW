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

namespace App\Controller\MetaData;

use App\Service\MetaData\InjectMetaDataManagerTrait;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MetaItemAdminController
 *
 */
class MetaItemAdminController extends CRUDController
{
    use InjectMetaDataManagerTrait;

    /**
     * @inheritDoc
     */
    protected function preList(Request $request): ?Response
    {
        $this->metaDataManager->createMetaItems();
        return null;
    }
}
