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

use App\Entity\Base\BaseEntityInterface;
use App\Entity\Onboarding\Inquiry;
use App\Service\InjectAdminManagerTrait;
use App\Service\Onboarding\InquiryManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InquiryControllerTrait
 * @property \Sonata\AdminBundle\Admin\AdminInterface $admin
 * @method createForm(string $type, $data = null, array $options = []): FormInterface
 * @method renderView(string $view, array $parameters = []): string
 * @method trans($id, array $parameters = [], $domain = null, $locale = null)
 * @method addFlash(string $type, $message)
 * @method redirect(string $url, int $status = 302): RedirectResponse
 * @method addRenderExtraParams(array $parameters = []): array
 */
trait InquiryControllerTrait
{
    use InjectAdminManagerTrait;

    /**
     * @param Request $request
     * @param InquiryManager $inquiryManager
     * @param BaseEntityInterface $entity
     *
     * @param string $formAction
     * @param string|null $backUrl
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function renderAskQuestion(
        Request $request,
        InquiryManager $inquiryManager,
        BaseEntityInterface $entity,
        string $formAction,
        string $backUrl = null
    ): Response
    {
        $isModal = $request->isXmlHttpRequest();
        $inquiry = new Inquiry();

        $form = $inquiryManager->createFormForEntity($inquiry, $entity, $formAction);

        $inquiryAdmin = $this->adminManager->getAdminByEntityClass(Inquiry::class);
        if ($entity instanceof Inquiry) {
            $referencedObject = $inquiryManager->getReferencedObject($entity->getReferenceSource(), $entity->getReferenceId());
            $parentInquiry = $entity;
            $checkRouteAccess = 'showQuestions';
        } else {
            $referencedObject = $entity;
            $parentInquiry = null;
            $checkRouteAccess = 'askQuestion';
        }
        if (!$backUrl) {
            $backUrl = $this->getInquiryBackUrl($referencedObject);
        }
        if (null !== $referencedObject) {
            $referencedAdmin = $this->adminManager->getAdminByEntityClass(get_class($referencedObject));
            if (null !== $referencedAdmin) {
                if (!$referencedAdmin->hasRoute($checkRouteAccess)
                    || !$referencedAdmin->hasAccess($checkRouteAccess, $referencedObject)) {
                    $messageText = $this->trans('app.inquiry.message.error');
                    $this->addFlash('danger', $messageText);
                    return $this->redirect($backUrl);
                }
            }
        }
        $form->handleRequest($request);
        $isSubmitted = $form->isSubmitted();
        if ($isSubmitted && $form->isValid()) {
            $inquiryManager->saveInquiry($inquiry, $entity);
            if ($isModal) {
                $jsonData = [
                    'type' => 'reload',
                    'status' => 200,
                    'content' => [],
                ];
                return new JsonResponse($jsonData);
            }
            $messageText = $this->trans('app.inquiry.message.success');
            $this->addFlash('success', $messageText);
            return $this->redirect($backUrl);
        }
        if (method_exists($referencedObject, 'getDescription')) {
            $showProperty = 'description';
        } else {
            $showProperty = null;
        }
        $viewParameters = [
            'action' => 'askQuestion',
            'form' => $form->createView(),
            'object' => $referencedObject,
            'parentInquiry' => $parentInquiry,
            'showProperty' => $showProperty,
            'inquiry' => $inquiry,
            'inquiryAdmin' => $inquiryAdmin,
            'isModal' => $isModal,
            'backUrl' => $backUrl,
        ];
        $data = $this->renderView('Onboarding/Inquiry/create.html.twig', $this->addRenderExtraParams($viewParameters));
        if ($isModal) {
            $jsonData = [
                'type' => 'content',
                'status' => 200,
                'content' => $data,
            ];
            return new JsonResponse($jsonData);
        }

        return new Response($data);
    }

    protected function getInquiryBackUrl(?BaseEntityInterface $referencedObject): ?string
    {
        if (null === $referencedObject) {
            return null;
        }
        $referencedAdmin = $this->adminManager->getAdminByEntityClass(get_class($referencedObject));

        $backUrl = null;
        if (null !== $referencedAdmin) {
            $parameters = [];
            if ($filter = $referencedAdmin->getFilterParameters()) {
                $parameters['filter'] = $filter;
            }
            if ($referencedAdmin->hasRoute('showQuestions')
                && $referencedAdmin->hasAccess('showQuestions', $referencedObject)) {
                $backUrl = $referencedAdmin->generateContextObjectUrl('showQuestions', $referencedObject);
            } else {
                $backUrl = $referencedAdmin->generateUrl('list', $parameters);
            }
        }
        return $backUrl;
    }
}
