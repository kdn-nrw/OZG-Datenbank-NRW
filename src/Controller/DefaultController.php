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


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DefaultController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */
class DefaultController extends AbstractController
{

    public function indexAction()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        } else {
            return $this->redirectToRoute('frontend_app_service_list');
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
