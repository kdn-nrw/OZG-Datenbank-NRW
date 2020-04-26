<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;


use App\Admin\ContextAwareAdminInterface;
use App\Admin\Frontend\AbstractFrontendAdmin;
use App\Admin\Frontend\ImplementationProjectAdmin;
use Sonata\AdminBundle\Controller\CRUDController;

/**
 * Class ImplementationProjectController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-24
 */
class ImplementationProjectController extends CRUDController
{

    public function index()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        }
        return $this->redirectToRoute('frontend_app_implementationproject_list');
    }

    /**
     * Contextualize the admin class depends on the current request.
     *
     * @throws \RuntimeException
     */
    protected function configure()
    {
        $request = $this->getRequest();
        $request->attributes->set('_sonata_admin', ImplementationProjectAdmin::class);
        parent::configure();
        /** @var $admin AbstractFrontendAdmin */
        $admin = $this->admin;
        $admin->setAppContext(ContextAwareAdminInterface::APP_CONTEXT_FE);
    }
}
