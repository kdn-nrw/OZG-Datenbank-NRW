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

    public function indexAction(Request $request): Response
    {
//        if ($this->isGranted('ROLE_ADMIN')) {
//            return $this->redirectToRoute('sonata_admin_dashboard');
//        }

        $parameters = [
            'base_template' => $request->isXmlHttpRequest() ?
                $this->templateRegistry->getTemplate('ajax') :
                'Frontend/Admin/base.html.twig',
            'admin_pool' => $this->pool,
        ];
        return $this->render('Frontend/dashboard.html.twig', $parameters);
    }
}
