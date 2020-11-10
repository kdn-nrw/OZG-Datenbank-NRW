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


use App\Api\Consumer\InjectApiManagerTrait;
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
    use InjectApiManagerTrait;

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
     * @param TranslatorInterface $translator
     */
    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
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
            $forms = [];
            $results = [];
            $consumerServices = $this->apiManager->getConfiguredConsumers();
            foreach ($consumerServices as $consumerService) {
                $actionUrl = $this->generateUrl('app_vsm_api_index', ['consumerKey' => $consumerService->getImportSourceKey()]);
                $demand = $consumerService->getDemand();
                $consumerService->initializeDemand($query);
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
                    'apiProvider' => $consumerService,
                ];
                $form = $this->createForm($consumerService->getFormTypeClass(), $demand, $formOptions);
                $form->handleRequest($request);
                $isValid = ($form->isSubmitted() && $form->isValid()) || (!empty($query) && $consumerKey === $consumerService->getImportSourceKey());
                // Only allow submission of the current provider
                if ($isValid) {
                    try {
                        $activeConsumerKey = $consumerService->getImportSourceKey();
                        $results[$consumerService->getImportSourceKey()] = $consumerService->search();
                    } catch (HttpExceptionInterface $e) {
                        /** @var FlashBag $flashBag */
                        $flashBag = $this->session->getFlashBag();
                        $translation = $this->translator->trans('app.api.common.search_exception', ['apiName' => $consumerService->getName()]);
                        $flashBag->add('danger', $translation . ' ' . $e->getMessage());
                    } catch (TransportExceptionInterface $e) {
                        /** @var FlashBag $flashBag */
                        $flashBag = $this->session->getFlashBag();
                        $translation = $this->translator->trans('app.api.common.search_exception');
                        $flashBag->add('danger', $translation . ' ' . $e->getMessage());
                    }
                }
                $forms[$consumerService->getImportSourceKey()] = $form->createView();
            }
            $response = $this->render('Vsm/api-index.html.twig', [
                'apiHandler' => $this->apiManager,
                'consumers' => $consumerServices,
                'forms' => $forms,
                'results' => $results,
                'activeConsumerKey' => $activeConsumerKey,
            ]);
            return $response;
        }
        return $this->redirectToRoute('frontend_app_service_list');
    }
}
