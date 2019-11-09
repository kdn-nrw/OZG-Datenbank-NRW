<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-08
 */

namespace App\Controller;


use App\Admin\Frontend\AbstractFrontendAdmin;
use App\Admin\Frontend\ServiceAdmin;
use Sonata\AdminBundle\Controller\CRUDController;

/**
 * Class ServiceController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-08
 */
class ServiceController extends CRUDController
{

    public function index()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        } else {
            die('TEST');
        }
//        $response = $this->render('home.html.twig');
//        $response->setStatusCode(Response::HTTP_NOT_FOUND);
//        $response->headers->add([
//            'X-Robots-Tag' => 'noindex, nofollow',
//        ]);
//
//        return $response;
    }

    /**
     * Contextualize the admin class depends on the current request.
     *
     * @throws \RuntimeException
     */
    protected function configure()
    {
        $request = $this->getRequest();
        $request->attributes->set('_sonata_admin', ServiceAdmin::class);
        parent::configure();
        /** @var $admin AbstractFrontendAdmin */
        $admin = $this->admin;
        $admin->setAppContext(AbstractFrontendAdmin::APP_CONTEXT_FE);
        $admin->setTemplate('list', 'Frontend/Admin/CRUD/list.html.twig');
        $admin->setTemplate('layout', 'Frontend/Admin/base.html.twig');
    }
}
