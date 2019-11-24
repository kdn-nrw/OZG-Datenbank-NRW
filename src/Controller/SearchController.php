<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-24
 */

namespace App\Controller;


use Sonata\AdminBundle\Admin\Pool;
use Sonata\DoctrineORMAdminBundle\Filter\Filter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class SearchController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-24
 */
class SearchController extends AbstractController
{
    /**
     * @var Pool
     */
    private $adminPool;

    /**
     * SearchController constructor.
     * @param Pool $adminPool
     */
    public function __construct(Pool $adminPool)
    {
        $this->adminPool = $adminPool;
    }


    public function index()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            //$adminClasses = $this->adminPool->getAdminClasses();
            $adminServiceIds = $this->adminPool->getAdminServiceIds();
            $adminList = [];
            foreach ($adminServiceIds as $serviceId) {
                // Initialize only app backend admins
                if (strpos($serviceId, 'App') === 0 && strpos($serviceId, 'Frontend') === false) {
                    $admin = $this->adminPool->getInstance($serviceId);
                    if ($admin->isGranted('LIST')) {
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
                            //'filters' => $filters,
                        ];
                    }
                }
            }
            $response = $this->render('Search/index.html.twig', ['adminList' => $adminList]);
            return $response;
        } else {
            return $this->redirectToRoute('frontend_app_service_list');
        }
    }
}
