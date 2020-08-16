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

namespace App\Controller;


use App\Admin\ContextAwareAdminInterface;
use App\Admin\Frontend\AbstractFrontendAdmin;
use App\Datagrid\CustomDatagrid;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractFrontendCRUDController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-08-16
 */
abstract class AbstractFrontendCRUDController extends CRUDController
{
    /**
     * Returns the default route name
     *
     * @return string
     */
    abstract protected function getDefaultRouteName(): string;

    /**
     * Returns the admin class name
     *
     * @return string
     */
    abstract protected function getAdminClassName(): string;

    public function index()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        }
        return $this->redirectToRoute($this->getDefaultRouteName());
    }

    /**
     * @inheritDoc
     */
    public function renderWithExtraParams($view, array $parameters = [], ?Response $response = null)
    {
        if (array_key_exists('datagrid', $parameters)) {
            $datagrid = $parameters['datagrid'];
            if ($datagrid instanceof CustomDatagrid) {
                $filterMenuItems = $datagrid->getFilterMenuItems();
                foreach ($filterMenuItems as $key => $filterMenuItem) {
                    if (count($filterMenuItem['valueEntities']) === 1) {
                        $valueEntity = current($filterMenuItem['valueEntities']);
                        $parameters[$filterMenuItem['parameter']] = $valueEntity;
                    }
                }
            }
        }
        return parent::renderWithExtraParams($view, $parameters, $response);
    }

    /**
     * Contextualize the admin class depends on the current request.
     *
     * @throws \RuntimeException
     */
    protected function configure()
    {
        /*
         * Set the frontend admin class in the request so it will be used in the parent configure function
         */
        $request = $this->getRequest();
        $request->attributes->set('_sonata_admin', $this->getAdminClassName());
        parent::configure();
        /** @var $admin AbstractFrontendAdmin */
        $admin = $this->admin;
        $admin->setAppContext(ContextAwareAdminInterface::APP_CONTEXT_FE);
    }
}
