<?php
/**
 * Mindbase 3
 *
 * PHP version 7.2
 *
 * @author    gert.hammes <gert.hammes@brain-appeal.com>
 * @copyright 2019 Brain Appeal GmbH (www.brain-appeal.com)
 * @license
 * @link      https://www.brain-appeal.com/
 * @since     2019-04-18
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @author    gert.hammes <gert.hammes@brain-appeal.com>
 * @copyright 2019 Brain Appeal GmbH (www.brain-appeal.com)
 * @license
 * @link      https://www.brain-appeal.com/
 * @since     2019-04-18
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
