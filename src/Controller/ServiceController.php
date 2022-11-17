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


use App\Admin\Frontend\ServiceAdmin;
use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\ServiceSystem;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ServiceController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-08
 */
class ServiceController extends AbstractFrontendCRUDController
{
    use InjectManagerRegistryTrait;

    /**
     * @inheritDoc
     */
    protected function getDefaultRouteName(): string
    {
        return 'frontend_app_service_list';
    }

    /**
     * @inheritDoc
     */
    protected function getAdminClassName(): string
    {
        return ServiceAdmin::class;
    }

    /**
     * Returns the service choices for the selected service systems
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getChoicesAction(Request $request): JsonResponse
    {
        $result = 'ERROR';
        $jsonData = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
            $changeData = $parametersAsArray['changeData'];
            $selectedChoices = $changeData['groupValues'];//array_filter($changeData['groupValues'], 'is_int');
            $selectedChoices[] = 0;
            $jsonData['selectedChoices'] = $selectedChoices;
            $em = $this->getEntityManager();
            /** @var EntityRepository $repository */
            $repository = $em->getRepository(ServiceSystem::class);
            $queryBuilder = $repository->createQueryBuilder('ss');
            $queryBuilder->where('ss IN (:selectedChoices)')
                ->setParameter('selectedChoices', $selectedChoices)
                ->orderBy('ss.name', 'ASC');
            $result = $queryBuilder->getQuery()->execute();
            $serviceList = [];
            $changedList = [];
            foreach ($result as $serviceSystem) {
                /** @var ServiceSystem $serviceSystem */
                $serviceSystemId = $serviceSystem->getId();
                $isCurrent = $serviceSystemId === (int)$changeData['groupId'];
                $services = $serviceSystem->getServices();
                foreach ($services as $service) {
                    $serviceList[] = [
                        'id' => $service->getId(),
                        'text' => $service->getName(),
                    ];
                    if ($isCurrent) {
                        $changedList[] = [
                            'id' => $service->getId(),
                            'text' => $service->getName(),
                        ];
                    }
                }
            }
            if ($changeData['type'] === 'removed'
                && null !== $removedServiceSystem = $repository->find((int)$changeData['groupId'])) {
                /** @var ServiceSystem $removedServiceSystem */
                $services = $removedServiceSystem->getServices();
                foreach ($services as $service) {
                    $changedList[] = [
                        'id' => $service->getId(),
                        'text' => $service->getName(),
                    ];
                }
            }
            $jsonData['serviceList'] = $serviceList;
            $result = 'SUCCESS';
            $jsonData[$changeData['type']] = $changedList;
        }
        $data = [
            'result' => $result,
            'data' => $jsonData,
        ];
        return $this->json($data);
    }
}
