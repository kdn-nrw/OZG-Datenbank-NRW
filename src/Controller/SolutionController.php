<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-24
 */

namespace App\Controller;


use App\Admin\Frontend\AbstractFrontendAdmin;
use App\Admin\Frontend\ImplementationProjectAdmin;
use App\Admin\Frontend\SolutionAdmin;
use Sonata\AdminBundle\Controller\CRUDController;

/**
 * Class SolutionController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-12-19
 */
class SolutionController extends CRUDController
{

    public function index()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        } else {
            return $this->redirectToRoute('frontend_app_solution_list');
        }
    }

    /**
     * Contextualize the admin class depends on the current request.
     *
     * @throws \RuntimeException
     */
    protected function configure()
    {
        $request = $this->getRequest();
        $request->attributes->set('_sonata_admin', SolutionAdmin::class);
        parent::configure();
        /** @var $admin AbstractFrontendAdmin */
        $admin = $this->admin;
        $admin->setAppContext(AbstractFrontendAdmin::APP_CONTEXT_FE);
    }
}
