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


use App\Admin\Frontend\ContextFrontendAdminInterface;
use App\Datagrid\CustomDatagrid;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\Base\NamedEntityInterface;
use App\Entity\Base\SluggableInterface;
use Behat\Transliterator\Transliterator;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionCollection;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AbstractFrontendCRUDController
 */
abstract class AbstractFrontendCRUDController extends DefaultCRUDController
{
    /**
     * Returns the default route name
     *
     * @return string
     */
    abstract protected function getDefaultRouteName(): string;

    /**
     * Returns the admin class name
     *
     * @return string
     */
    abstract protected function getAdminClassName(): string;

    public function index(): RedirectResponse
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        }
        return $this->redirectToRoute($this->getDefaultRouteName());
    }

    /**
     * Show action.
     *
     * @param Request $request
     * @param string $slug
     *
     * @return Response
     */
    public function showBySlugAction(Request $request, string $slug): ?Response
    {
        if (is_numeric($slug) && (int)$slug > 0) {
            $object = $this->admin->getObject((int)$slug);
            if ($object instanceof SluggableInterface
                && ($redirectSlug = $object->getSlug())
                && $redirectSlug !== $slug) {
                $redirectUrl = $this->admin->generateUrl('show', ['slug' => $redirectSlug]);
                $status = 301;
                return $this->redirect($redirectUrl, $status);
            }
        } elseif (is_a($this->admin->getClass(), SluggableInterface::class, true)) {
            $modelManager = $this->admin->getModelManager();
            $object = $modelManager->findOneBy($this->admin->getClass(), ['slug' => $slug]);
            if (($object instanceof NamedEntityInterface) && strlen($slug) < 4
                && ($name = $object->getName()) && strlen($name) > 3) {
                /** @var SluggableInterface $object */
                $newSlug = Transliterator::urlize($name);
                if (strlen($newSlug) > strlen($slug)) {
                    $entityClass = $this->admin->getClass();
                    $checkObject = $modelManager->findOneBy($entityClass, ['slug' => $newSlug]);
                    if (null === $checkObject && $modelManager instanceof ModelManager) {
                        $object->setSlug($newSlug);
                        $redirectUrl = $this->admin->generateUrl('show', ['slug' => $newSlug]);
                        $status = 301;
                        $modelManager->getEntityManager($entityClass)->flush();
                        return $this->redirect($redirectUrl, $status);
                    }
                }
            }
        }
        if (null === $object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with path: %s', $slug));
        }
        /** @var BaseEntityInterface $object */
        return $this->showObject($request, $object);
    }

    /**
     * Renders the show view
     * @param Request $request
     * @param BaseEntityInterface $object
     * @return Response
     */
    private function showObject(Request $request, BaseEntityInterface $object): Response
    {
        $this->admin->checkAccess('show', $object);

        $preResponse = $this->preShow($request, $object);
        if (null !== $preResponse) {
            return $preResponse;
        }

        $this->admin->setSubject($object);

        $fields = $this->admin->getShow();
        \assert($fields instanceof FieldDescriptionCollection);

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplateRegistry()->getTemplate('show');
        //$template = $this->templateRegistry->getTemplate('show');

        return $this->renderWithExtraParams($template, [
            'action' => 'show',
            'object' => $object,
            'elements' => $fields,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function showAction(Request $request): Response
    {
        $id = $request->get($this->admin->getIdParameter());
        $object = null;
        if (is_numeric($id)) {
            $object = $this->admin->getObject($id);
        } elseif (!empty($id)) {
            $modelManager = $this->admin->getModelManager();
            $object = $modelManager->findOneBy($this->admin->getClass(), ['slug' => $id]);
        }
        if ($object instanceof SluggableInterface
            && ($redirectSlug = $object->getSlug())
            && (string)$id !== $redirectSlug) {
            $redirectUrl = $this->admin->generateUrl('show', ['slug' => $redirectSlug]);
            $status = 301;
            return $this->redirect($redirectUrl, $status);
        }
        if (null === $object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        return parent::showAction($request);
    }

    /**
     * No delete action possible in frontend (the routes are not defined anyway)
     * @inheritDoc
     */
    public function deleteAction(Request $request): Response
    {
        return $this->redirectToRoute($this->getDefaultRouteName());
    }

    /**
     * No edit action possible in frontend (the routes are not defined anyway)
     * @inheritDoc
     */
    public function editAction(Request $request): Response
    {
        return $this->redirectToRoute($this->getDefaultRouteName());
    }

    /**
     * No create action possible in frontend (the routes are not defined anyway)
     * @inheritDoc
     */
    public function createAction(Request $request): Response
    {
        return $this->redirectToRoute($this->getDefaultRouteName());
    }

    /**
     * Export data to specified format.
     *
     * @throws AccessDeniedException If access is not granted
     * @throws \RuntimeException     If the export format is invalid
     *
     * @return Response
     */
    public function exportAction(Request $request): Response
    {
        try {
            return parent::exportAction($request);
        } catch (\RuntimeException $e) {
            $messageText = 'Ungültiges Export-Format angegeben';//$this->trans('app.inquiry.message.error');
            $this->addFlash('warning', $messageText);
        }
        return $this->redirectToList();
    }

    protected function addRenderExtraParams(array $parameters = []): array
    {
        $parameters = parent::addRenderExtraParams($parameters);
        if (array_key_exists('datagrid', $parameters)) {
            $datagrid = $parameters['datagrid'];
            if ($datagrid instanceof CustomDatagrid) {
                $filterMenuItems = $datagrid->getFilterMenuItems();
                foreach ($filterMenuItems as $filterMenuItem) {
                    if (count($filterMenuItem['valueEntities']) === 1) {
                        $valueEntity = current($filterMenuItem['valueEntities']);
                        $parameters[$filterMenuItem['parameter']] = $valueEntity;
                    }
                }
            }
        }

        return $parameters;
    }

    /**
     * Contextualize the admin class depends on the current request.
     *
     * @throws \RuntimeException
     */
    final public function configureFrontendController(Request $request)
    {
        /*
         * Set the frontend admin class in the request, so it will be used in the parent configure function
         */
        $request->attributes->set('_sonata_admin', $this->getAdminClassName());
        if (!$this->admin) {
            $this->configureAdmin($request);
        }
        $admin = $this->admin;
        if ($admin instanceof ContextFrontendAdminInterface) {
            $admin->initializeAppContext();
        }
    }
}
