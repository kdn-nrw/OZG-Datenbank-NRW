<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */
class DefaultController extends AbstractController
{

    public function index()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        } else {
            return $this->redirectToRoute('sonata_user_admin_security_login');
        }
//        $response = $this->render('home.html.twig');
//        $response->setStatusCode(Response::HTTP_NOT_FOUND);
//        $response->headers->add([
//            'X-Robots-Tag' => 'noindex, nofollow',
//        ]);
//
//        return $response;
    }
}
