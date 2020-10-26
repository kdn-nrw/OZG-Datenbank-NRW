<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;


use App\Admin\ExtendedSearchAdminInterface;
use App\Entity\Search;
use App\Form\Type\SearchType;
use App\Service\Zsm\ApiHandler;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\DoctrineORMAdminBundle\Filter\Filter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class VSMController
 */
class VSMController extends AbstractController
{
    /**
     * @var ApiHandler
     */
    protected $apiHandler;

    /**
     * VSMController constructor.
     * @param ApiHandler $apiHandler
     */
    public function __construct(ApiHandler $apiHandler)
    {
        $this->apiHandler = $apiHandler;
    }

    public function snippetAction()
    {
        if ($this->isGranted('ROLE_VSM')) {
            $response = $this->render('Vsm/snippet.html.twig', [
                'displayMap' => false
            ]);
            return $response;
        }
        return $this->redirectToRoute('frontend_app_service_list');
    }

    public function snippetMapAction()
    {
        if ($this->isGranted('ROLE_VSM')) {
            $response = $this->render('Vsm/snippet.html.twig', [
                'displayMap' => true
            ]);
            return $response;
        }
        return $this->redirectToRoute('frontend_app_service_list');
    }

    public function apiIndexAction()
    {
        if ($this->isGranted('ROLE_VSM')) {
            $activeProviderKey = null;
            $response = $this->render('Vsm/api-index.html.twig', [
                'apiHandler' => $this->apiHandler,
                'activeProviderKey' => $activeProviderKey,
            ]);
            return $response;
        }
        return $this->redirectToRoute('frontend_app_service_list');
    }
}
