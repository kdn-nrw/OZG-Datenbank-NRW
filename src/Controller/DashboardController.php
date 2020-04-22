<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-04-13
 */

namespace App\Controller;

use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DashboardController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-04-13
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
