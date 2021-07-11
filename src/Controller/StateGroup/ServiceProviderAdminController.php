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

namespace App\Controller\StateGroup;

use App\Admin\StateGroup\ServiceProviderAdmin;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ServiceProviderAdminController
 *
 */
class ServiceProviderAdminController extends CRUDController
{

    /**
     * This method can be overloaded in your custom CRUD controller.
     * It's called from editAction.
     *
     * @param object $object
     *
     * @return Response|null
     */
    protected function preEdit(Request $request, $object)
    {
        if ($this->admin instanceof ServiceProviderAdmin) {
            $this->admin->initializeServiceProviderDataCenter($object);
        }
        return parent::preEdit($request, $object);
    }
}
