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


use App\Api\Consumer\ApiHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
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
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * VSMController constructor.
     * @param SessionInterface $session
     * @param ApiHandler $apiHandler
     * @param TranslatorInterface $translator
     */
    public function __construct(SessionInterface $session, ApiHandler $apiHandler, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->apiHandler = $apiHandler;
        $this->translator = $translator;
    }

    public function snippetAction(): Response
    {
        if ($this->isGranted('ROLE_VSM')) {
            $response = $this->render('Vsm/snippet.html.twig', [
                'displayMap' => false
            ]);
            return $response;
        }
        return $this->redirectToRoute('frontend_app_service_list');
    }

    public function snippetMapAction(): Response
    {
        if ($this->isGranted('ROLE_VSM')) {
            $response = $this->render('Vsm/snippet.html.twig', [
                'displayMap' => true
            ]);
            return $response;
        }
        return $this->redirectToRoute('frontend_app_service_list');
    }

    /**
     * @param Request $request
     * @param string|null $consumerKey
     * @param string|null $query
     * @param int $page
     * @return Response
     */
    public function apiIndexAction(Request $request, ?string $consumerKey = null, ?string $query = null, int $page = 1): Response
    {
        if ($this->isGranted('ROLE_VSM')) {
            $activeConsumerKey = $consumerKey;
            $providers = $this->apiHandler->getConsumers();
            $forms = [];
            $results = [];
            foreach ($providers as $provider) {
                $actionUrl = $this->generateUrl('app_vsm_api_index', ['consumerKey' => $provider->getKey()]);
                $demand = $provider->getDemand($query);
                if ($page > 1) {
                    $demand->setPage($page);
                }
                $formOptions = [
                    'action' => $actionUrl,
                    'method' => 'POST',
                    'attr' => [
                        'novalidate' => 'novalidate',
                    ],
                    'data_class' => get_class($demand),
                    'apiProvider' => $provider,
                ];
                $form = $this->createForm($provider->getFormTypeClass(), $demand, $formOptions);
                $form->handleRequest($request);
                $isValid = ($form->isSubmitted() && $form->isValid()) || (!empty($query) && $consumerKey === $provider->getKey());
                // Only allow submission of the current provider
                if ($isValid) {
                    try {
                        $activeConsumerKey = $provider->getKey();
                        $results[$provider->getKey()] = $provider->search();
                    } catch (HttpExceptionInterface $e) {
                        /** @var FlashBag $flashBag */
                        $flashBag = $this->session->getFlashBag();
                        $translation = $this->translator->trans('app.api.common.search_exception', ['apiName' => $provider->getName()]);
                        $flashBag->add('danger', $translation . ' ' . $e->getMessage());
                    } catch (TransportExceptionInterface $e) {
                        /** @var FlashBag $flashBag */
                        $flashBag = $this->session->getFlashBag();
                        $translation = $this->translator->trans('app.api.common.search_exception');
                        $flashBag->add('danger', $translation . ' ' . $e->getMessage());
                    }
                }
                $forms[$provider->getKey()] = $form->createView();
            }
            $response = $this->render('Vsm/api-index.html.twig', [
                'apiHandler' => $this->apiHandler,
                'forms' => $forms,
                'results' => $results,
                'activeConsumerKey' => $activeConsumerKey,
            ]);
            return $response;
        }
        return $this->redirectToRoute('frontend_app_service_list');
    }
}
