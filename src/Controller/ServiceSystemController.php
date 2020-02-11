<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-11-02
 */

namespace App\Controller;


use App\Admin\Frontend\AbstractFrontendAdmin;
use App\Admin\Frontend\ServiceSystemAdmin;
use Sonata\AdminBundle\Controller\CRUDController;

/**
 * Class ServiceSystemController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-11-02
 */
class ServiceSystemController extends CRUDController
{

    public function index()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        }
        return $this->redirectToRoute('frontend_app_servicesystem_list');
    }

    /**
     * Contextualize the admin class depends on the current request.
     *
     * @throws \RuntimeException
     */
    protected function configure()
    {
        $request = $this->getRequest();
        $request->attributes->set('_sonata_admin', ServiceSystemAdmin::class);
        parent::configure();
        /** @var $admin AbstractFrontendAdmin */
        $admin = $this->admin;
        $admin->setAppContext(AbstractFrontendAdmin::APP_CONTEXT_FE);
    }
}
