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
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class AbstractFrontendCRUDController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-08-16
 */
abstract class AbstractFrontendCRUDController extends CRUDController
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

    public function index()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        }
        return $this->redirectToRoute($this->getDefaultRouteName());
    }

    /**
     * Show action.
     *
     * @param string $slug
     *
     * @return Response
     * @throws AccessDeniedException If access is not granted
     *
     * @throws NotFoundHttpException If the object does not exist
     */
    public function showBySlugAction(string $slug): ?Response
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
        return $this->showObject($object);
    }

    /**
     * Renders the show view
     * @param BaseEntityInterface $object
     * @return Response
     */
    private function showObject($object): Response
    {
        $request = $this->getRequest();

        $this->admin->checkAccess('show', $object);

        $preResponse = $this->preShow($request, $object);
        if (null !== $preResponse) {
            return $preResponse;
        }

        $this->admin->setSubject($object);

        $fields = $this->admin->getShow();
        \assert($fields instanceof \Sonata\AdminBundle\Admin\FieldDescriptionCollection);

        // NEXT_MAJOR: Remove this line and use commented line below it instead
        $template = $this->admin->getTemplate('show');
        //$template = $this->templateRegistry->getTemplate('show');

        return $this->renderWithExtraParams($template, [
            'action' => 'show',
            'object' => $object,
            'elements' => $fields,
        ], null);
    }

    /**
     * @inheritDoc
     */
    public function showAction($deprecatedId = null)
    {
        $request = $this->getRequest();
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
        return parent::showAction();
    }

    /**
     * No delete action possible in frontend (the routes are not defined anyways)
     * @inheritDoc
     */
    public function deleteAction($id) // NEXT_MAJOR: Remove the unused $id parameter
    {
        return $this->redirectToRoute($this->getDefaultRouteName());
    }

    /**
     * No edit action possible in frontend (the routes are not defined anyways)
     * @inheritDoc
     */
    public function editAction($deprecatedId = null)
    {
        return $this->redirectToRoute($this->getDefaultRouteName());
    }

    /**
     * No create action possible in frontend (the routes are not defined anyways)
     * @inheritDoc
     */
    public function createAction()
    {
        return $this->redirectToRoute($this->getDefaultRouteName());
    }

    /**
     * @inheritDoc
     */
    public function renderWithExtraParams($view, array $parameters = [], ?Response $response = null)
    {
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
        return parent::renderWithExtraParams($view, $parameters, $response);
    }

    /**
     * Contextualize the admin class depends on the current request.
     *
     * @throws \RuntimeException
     */
    protected function configure()
    {
        /*
         * Set the frontend admin class in the request so it will be used in the parent configure function
         */
        $request = $this->getRequest();
        $request->attributes->set('_sonata_admin', $this->getAdminClassName());
        parent::configure();
        $admin = $this->admin;
        if ($admin instanceof ContextFrontendAdminInterface) {
            $admin->initializeAppContext();
        }
    }
}
