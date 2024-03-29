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


use App\Admin\ExtendedSearchAdminInterface;
use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Search;
use App\Form\Type\SearchType;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\DoctrineORMAdminBundle\Filter\Filter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SearchController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-24
 */
class SearchController extends AbstractController
{
    use InjectManagerRegistryTrait;

    /**
     * @var Pool
     */
    private $adminPool;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * SearchController constructor.
     * @param SessionInterface $session
     * @param Pool $adminPool
     * @param TranslatorInterface $translator
     */
    public function __construct(SessionInterface $session, Pool $adminPool, TranslatorInterface $translator)
    {
        $this->adminPool = $adminPool;
        $this->translator = $translator;
        $this->session = $session;
    }


    public function indexAction()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $search = $this->initSearchModel(null);
            $adminList = $this->createAdminList();
            $searchForm = $this->initSearchForm($search, $adminList);
            $repository = $this->getEntityManager()->getRepository(Search::class);
            $searchList = $repository->findAll();
            $response = $this->render('Search/index.html.twig', [
                'adminList' => $adminList,
                'showList' => true,
                'searchList' => $searchList,
                'searchForm' => $searchForm->createView(),
            ]);
            return $response;
        }
        return $this->redirectToRoute('frontend_app_modelregion_list');
    }

    /**
     * Edit the search
     *
     * @param int|null $id
     * @return Response
     */
    public function editAction(int $id): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $search = $this->initSearchModel($id);
            $adminList = $this->createAdminList();
            $searchForm = $this->initSearchForm($search, $adminList);
            $response = $this->render('Search/index.html.twig', [
                'adminList' => $adminList,
                'showList' => false,
                'searchForm' => $searchForm->createView(),
            ]);
            return $response;
        }
        return $this->redirectToRoute('frontend_app_modelregion_list');
    }

    /**
     * Edit the search
     *
     * @param int|null $id
     * @return Response
     */
    public function deleteAction(int $id): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $search = $this->initSearchModel($id);
            /** @var UserInterface $currentUser */
            $currentUser = $this->getUser();
            $search->setUser($currentUser);
            if (null !== $search && ($search->getUser() === $currentUser || $search->isShowForAll())) {
                $em = $this->getEntityManager();
                $em->remove($search);
                $em->flush();
                /** @var FlashBag $flashBag */
                $flashBag = $this->session->getFlashBag();
                $translation = $this->translator->trans('app.search.entity.delete_success');
                $flashBag->add('success', $translation);
            }
            return $this->redirectToRoute('app_search_list', []);
        }
        return $this->redirectToRoute('frontend_app_modelregion_list');
    }

    /**
     * Save the search
     *
     * @param Request $request
     * @param int|null $id
     * @return RedirectResponse|Response
     */
    public function saveAction(Request $request, ?int $id)
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $search = $this->initSearchModel($id);
            $adminList = $this->createAdminList();
            $searchForm = $this->initSearchForm($search, $adminList);
            $searchForm->handleRequest($request);
            if ($searchForm->isSubmitted() && $searchForm->isValid()) {
                $search->updateParameters();
                if (null === $search->getUser()) {
                    /** @var UserInterface $currentUser */
                    $currentUser = $this->getUser();
                    $search->setUser($currentUser);
                }
                $em = $this->getEntityManager();
                if (null === $search->getId()) {
                    $em->persist($search);
                }
                $em->flush();
                /** @var FlashBag $flashBag */
                $flashBag = $this->session->getFlashBag();
                $translation = $this->translator->trans('app.search.entity.save_success');
                $flashBag->add('success', $translation);
            }
            return $this->redirectToRoute('app_search_list', []);
        }
        return $this->redirectToRoute('frontend_app_modelregion_list');
    }

    /**
     * Creates the admin list with she searchable admins
     * @return array
     */
    private function createAdminList(): array
    {
        $adminServiceIds = $this->adminPool->getAdminServiceIds();
        $adminList = [];
        foreach ($adminServiceIds as $serviceId) {
            // Initialize only app backend admins
            if (strpos($serviceId, 'App') === 0 && strpos($serviceId, 'Frontend') === false) {
                $admin = $this->adminPool->getInstance($serviceId);
                if ($admin instanceof ExtendedSearchAdminInterface && $admin->isGranted('LIST')) {
                    //$routes = $admin->getRoutes();
                    $arrayRoute = $admin->generateMenuUrl('list');
                    $label = $admin->getLabel();
                    $dataGrid = $admin->getDatagrid();
                    $formView = $dataGrid->getForm()->createView();
                    $filters = $dataGrid->getFilters();
                    foreach ($filters as $filter) {
                        /** @var Filter $filter */
                        $filter->setOption('show_filter', true);
                    }
                    $adminList[$serviceId] = [
                        'id' => $serviceId,
                        'internalId' => strtolower(str_replace('\\', '-', $serviceId)),
                        'label' => $label,
                        'admin' => $admin,
                        'form' => $formView,
                        'filterRoute' => $arrayRoute['route'],
                        //'filters' => $filters,
                    ];
                }
            }
        }
        return $adminList;
    }

    /**
     * Initializes the search model
     *
     * @param int|null $id
     * @return Search
     */
    private function initSearchModel(?int $id): Search
    {
        $search = null;
        if ($id) {
            /** @var Search|null $search */
            $search = $this->getEntityManager()->find(Search::class, $id);
            if (null === $search->getUser()) {
                /** @var UserInterface $currentUser */
                $currentUser = $this->getUser();
                $search->setUser($currentUser);
            }
        }
        if (null === $search) {
            $search = new Search();
        }
        return $search;
    }

    /**
     * Initializes the search form
     *
     * @param Search $search
     * @param array $adminList
     * @return FormInterface
     */
    private function initSearchForm(Search $search, array $adminList)
    {
        $actionUrl = $this->generateUrl(
            'app_search_save',
            ['id' => $search->getId()]
        );
        $adminChoices = [];
        foreach ($adminList as $adminId => $adminData) {
            $adminChoices[$adminData['label']] = $adminId;
        }
        $formOptions = [
            'action' => $actionUrl,
            'method' => 'POST',
            'attr' => [
                'novalidate' => 'novalidate',
            ],
            'admin_choices' => $adminChoices,
        ];
        return $this->createForm(SearchType::class, $search, $formOptions);
    }
}
