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
use App\Form\Type\InquiryType;
use App\Service\Onboarding\InquiryManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InquiryControllerTrait
 * @method createForm(string $type, $data = null, array $options = []): FormInterface
 * @method renderView(string $view, array $parameters = []): string
 * @method trans($id, array $parameters = [], $domain = null, $locale = null)
 * @method addFlash(string $type, $message)
 * @method redirect(string $url, int $status = 302): RedirectResponse
 * @method addRenderExtraParams(array $parameters = []): array
 */
trait InquiryControllerTrait
{
    /**
     * @param Request $request
     * @param InquiryManager $inquiryManager
     * @param BaseEntityInterface $entity
     *
     * @param string $formAction
     * @param string $backUrl
     * @return Response
     */
    protected function renderAskQuestion(
        Request $request,
        InquiryManager $inquiryManager,
        BaseEntityInterface $entity,
        string $formAction,
        string $backUrl
    ): Response
    {
        $isModal = $request->isXmlHttpRequest();
        $inquiry = new Inquiry();
        $inquiry->setReferenceId($entity->getId());
        $inquiry->setReferenceSource(get_class($entity));

        $form = $this->createForm(InquiryType::class, $inquiry, [
            'action' => $formAction,
        ]);

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
        $viewParameters = [
            'action' => 'askQuestion',
            'form' => $form->createView(),
            'object' => $entity,
            'inquiry' => $inquiry,
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
}
