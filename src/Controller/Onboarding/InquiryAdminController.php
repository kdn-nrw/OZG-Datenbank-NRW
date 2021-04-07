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

use App\Entity\Onboarding\Inquiry;
use App\Form\Type\InquiryType;
use App\Service\Onboarding\InquiryManager;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InquiryAdminController
 *
 */
class InquiryAdminController extends CRUDController
{
    /**
     * @param Request $request
     * @param InquiryManager $inquiryManager
     * @param string $referenceSource
     * @param int|null $referenceId
     * @return Response
     */
    public function questionAction(Request $request, InquiryManager $inquiryManager, string $referenceSource, ?int $referenceId): Response
    {
        $isModal = $request->isXmlHttpRequest();
        $inquiry = $this->admin->getNewInstance();
        /** @var Inquiry $inquiry */
        $inquiry->setReferenceId($referenceId);
        $inquiry->setReferenceSource($referenceSource);

        $form = $this->createForm(InquiryType::class, $inquiry, [
            'action' => $this->admin->generateUrl('question', ['referenceSource' => $referenceSource, 'referenceId' => $referenceId]),
        ]);

        $form->handleRequest($request);
        $isSubmitted = $form->isSubmitted();
        if ($isSubmitted && $form->isValid()) {
            $inquiryManager->saveInquiry($inquiry, null);
            if ($isModal) {
                $jsonData = [
                    'type'    => 'reload',
                    'status'  => 200,
                    'content' => [],
                ];
                return new JsonResponse($jsonData);
            }
            $messageText = $this->trans('app.inquiry.message.success');
            $this->addFlash('success', $messageText);
            return $this->redirectToList();
        }

        $data = $this->renderView('Onboarding/Inquiry/create.html.twig', [
            'form' => $form->createView(),
            'inquiry' => $inquiry,
            'isModal' => $isModal,
        ]);
        if ($isModal) {
            $jsonData = [
                'type'    => 'content',
                'status'  => 200,
                'content' => $data,
            ];
            return new JsonResponse($jsonData);
        }

        return new Response($data);
    }
}
