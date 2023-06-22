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

use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class DashboardController
 */
class DashboardController extends AbstractController
{

    /**
     * @var TemplateRegistryInterface
     */
    private $templateRegistry;

    /**
     * @var Pool
     */
    private $pool;

    public function __construct(
        TemplateRegistryInterface $templateRegistry,
        Pool $pool
    ) {
        $this->templateRegistry = $templateRegistry;
        $this->pool = $pool;
    }

    public function indexAction(): Response
    {
//        if ($this->isGranted('ROLE_ADMIN')) {
//            return $this->redirectToRoute('sonata_admin_dashboard');
//        }
/*
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $parameters = [
            'base_template' => $request->isXmlHttpRequest() ?
                $this->templateRegistry->getTemplate('ajax') :
                'Frontend/Admin/base.html.twig',
            'admin_pool' => $this->pool,
            'error' => $error,
            'lastUsername' => $lastUsername,
        ];*/
        return $this->redirectToRoute('frontend_app_modelregion_list');
    }
}
