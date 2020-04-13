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
     * @var array
     */
    private $dashboardBlocks;

    /**
     * @var TemplateRegistryInterface
     */
    private $templateRegistry;

    /**
     * @var Pool
     */
    private $pool;

    public function __construct(
        array $dashboardBlocks,
        TemplateRegistryInterface $templateRegistry,
        Pool $pool
    ) {
        $this->dashboardBlocks = $dashboardBlocks;
        $this->templateRegistry = $templateRegistry;
        $this->pool = $pool;
    }

    public function indexAction(Request $request): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        }
        $blocks = [
            'top' => [],
            'left' => [],
            'center' => [],
            'right' => [],
            'bottom' => [],
        ];
        foreach ($this->dashboardBlocks as $block) {
            if (empty($block['settings']['disable_public'])) {
                if (isset($block['settings']['mode'])) {
                    $block['settings']['mode'] = 'public';
                }
                $blocks[$block['position']][] = $block;
            }
        }

        $parameters = [
            'base_template' => $request->isXmlHttpRequest() ?
                $this->templateRegistry->getTemplate('ajax') :
                'Frontend/Admin/base.html.twig',
            'admin_pool' => $this->pool,
            'blocks' => $blocks,
        ];

        return $this->render($this->templateRegistry->getTemplate('dashboard'), $parameters);
    }
}
