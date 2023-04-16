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

namespace App\Controller\Onboarding;

use App\Controller\DefaultCRUDController;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\Onboarding\Inquiry;
use App\Service\Onboarding\InjectInquiryManagerTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class InquiryAdminController
 *
 */
class InquiryAdminController extends DefaultCRUDController
{
    use InquiryControllerTrait;
    use InjectInquiryManagerTrait;

    /**
     * @param Request $request
     * @param string $referenceSource
     * @param int|null $referenceId
     * @return Response
     */
    public function questionAction(Request $request, string $referenceSource, ?int $referenceId): Response
    {
        $object = $this->inquiryManager->getReferencedObject($referenceSource, $referenceId);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $referenceId));
        }
        $formAction = $this->admin->generateUrl('question', ['referenceSource' => $referenceSource, 'referenceId' => $referenceId]);
        //$formAction = $this->admin->generateObjectUrl('askQuestion', $object);
        /** @var BaseEntityInterface $object */
        return $this->renderAskQuestion($request, $this->inquiryManager, $object, $formAction);
    }

    /**
     * Ask a question for the given object id
     *
     * @param Request $request
     */
    public function askQuestionAction(Request $request): Response
    {
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }
        $formAction = $this->admin->generateObjectUrl('askQuestion', $object);
        /** @var Inquiry $object */
        return $this->renderAskQuestion($request, $this->inquiryManager, $object, $formAction);
    }

    /**
     * Redirect the user depend on this choice.
     * Redirect to referenced entity after inquiry was edited
     *
     * @param object $object
     *
     * @return RedirectResponse
     */
    protected function redirectTo(Request $request, object $object): RedirectResponse
    {
        if ($object instanceof Inquiry) {
            $referencedObject = $this->inquiryManager->getReferencedObject($object->getReferenceSource(), $object->getReferenceId());
            if ($backUrl = $this->getInquiryBackUrl($referencedObject)) {
                return new RedirectResponse($backUrl);
            }
        }
        return parent::redirectTo($request, $object);
    }

    protected function addRenderExtraParams(array $parameters = []): array
    {
        $parameters = parent::addRenderExtraParams($parameters);
        if ($parameters['action'] === 'show' && $parameters['object'] instanceof Inquiry) {
            $object = $parameters['object'];
            $referencedObject = $this->inquiryManager->getReferencedObject($object->getReferenceSource(), $object->getReferenceId());
            $parameters['referencedObject'] = $referencedObject;
            $parameters['inquiryAdmin'] = $this->admin;
            $parameters['referenceSource'] = $object->getReferenceSource();
            if (null !== $referencedObject) {
                $parameters['inquiries'] = $this->inquiryManager->findEntityInquiries($referencedObject);
            } else {
                $parameters['inquiries'] = [];
            }
        }

        return $parameters;
    }
}
