<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Admin\StateGroup\CommuneAdmin;
use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\StateGroup\Commune;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class CommuneAdminController
 *
 */
class CommuneAdminController extends CRUDController
{
    use InjectManagerRegistryTrait;

    /**
     * Contextualize the admin class depends on the current request.
     *
     * @throws \RuntimeException
     */
    protected function configure()
    {
        $request = $this->getRequest();

        $adminCode = $request->get('_sonata_admin');
        if (!$adminCode) {
            // Not set for custom routes, e.g. app_commune_integrations
            $request->attributes->set('_sonata_admin', CommuneAdmin::class);
        }
        parent::configure();
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function integrationsAction(Request $request, int $id): Response
    {
        $commune = $this->getEntityManager()->find(Commune::class, $id);
        if (null === $commune || !$this->admin->isGranted('SHOW')) {
            $this->createAccessDeniedException();
        }
        return $this->renderPartialTemplate(
            $commune,
            'communeSolutions',
            'CommuneAdmin/show-commune-solution-integration.html.twig'
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function solutionsAction(Request $request, int $id): Response
    {
        $commune = $this->getEntityManager()->find(Commune::class, $id);
        if (null === $commune || !$this->admin->isGranted('SHOW')) {
            $this->createAccessDeniedException();
        }
        return $this->renderPartialTemplate(
            $commune,
            'solutions',
            'CommuneAdmin/show-commune-solutions.html.twig'
        );
    }

    /**
     * @param BaseEntityInterface $object
     * @param string $property
     * @param string $view
     * @return Response
     */
    protected function renderPartialTemplate(BaseEntityInterface $object, string $property, string $view): Response
    {
        $show = $this->admin->getShow();
        if (null === $show) {
            $this->createAccessDeniedException();
        }
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $fieldDescription = $show->get($property);
        $parameters = [
            'entities' => $propertyAccessor->getValue($object, $property),
            'object' => $object,
            'field_description' => $fieldDescription,
        ];

        $response = $this->renderWithExtraParams($view, $parameters);
        $jsonData = [
            'type' => 'content',
            'status' => 200,
            'content' => $response->getContent(),
        ];
        return new JsonResponse($jsonData);
    }

}
