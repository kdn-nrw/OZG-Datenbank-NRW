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


use App\Admin\Onboarding\AbstractOnboardingAdmin;
use App\Entity\Onboarding\AbstractOnboardingEntity;
use App\Entity\Onboarding\Inquiry;
use App\Service\Onboarding\InjectOnboardingManagerTrait;
use App\Service\Onboarding\InquiryManager;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AbstractOnboardingAdminController
 */
abstract class AbstractOnboardingAdminController extends CRUDController
{
    use InjectOnboardingManagerTrait;
    use InquiryControllerTrait;

    /**
     * Contextualize the admin class depends on the current request.
     *
     * @throws \RuntimeException
     */
    protected function configure()
    {
        parent::configure();
        $templateRegistry = $this->admin->getTemplateRegistry();
        if (null !== $templateRegistry) {
            $templateRegistry->setTemplate('edit', 'Onboarding/edit.html.twig');
        }
    }

    /**
     * @inheritDoc
     */
    protected function preList(Request $request)
    {
        $this->onboardingManager->createItems($this->admin->getClass());
        return null;
    }

    /**
     * Show questions for the given object id
     *
     * @param Request $request
     * @param InquiryManager $inquiryManager
     */
    public function showQuestionsAction(Request $request, InquiryManager $inquiryManager): Response
    {
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object || !$this->admin->hasAccess('showQuestions', $object)) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var AbstractOnboardingAdmin $admin */
        $admin = $this->admin;
        $inquiryAdmin = $this->adminManager->getAdminByEntityClass(Inquiry::class);
        /** @var AbstractOnboardingEntity $object */
        $inquiries = $inquiryManager->findEntityInquiries($object);
        //$formAction = $this->admin->generateObjectUrl('showQuestions', $object);
        // Mark question as read if question is opened by user with limited access or if question is directed at the
        // current user
        $onlyForRecipient = $admin->getCurrentUserCommuneLimits() === true;
        $inquiryManager->markInquiryListAsRead($inquiries, $onlyForRecipient);

        $parameters = [];
        if ($filter = $this->admin->getFilterParameters()) {
            $parameters['filter'] = $filter;
        }
        $backUrl = $this->admin->generateUrl('list', $parameters);
        $viewParameters = [
            'action' => 'showQuestions',
            'object' => $object,
            'inquiryAdmin' => $inquiryAdmin,
            'inquiries' => $inquiries,
            'referenceSource' => get_class($object),
            'backUrl' => $backUrl,
        ];
        $data = $this->renderView('Onboarding/Inquiry/list.html.twig', $this->addRenderExtraParams($viewParameters));

        return new Response($data);
    }

    /**
     * Ask a question for the given object id
     *
     * @param Request $request
     * @param InquiryManager $inquiryManager
     */
    public function askQuestionAction(Request $request, InquiryManager $inquiryManager): Response
    {
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object || !$this->admin->hasAccess('askQuestion', $object)) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }
        $formAction = $this->admin->generateObjectUrl('askQuestion', $object);

        $parameters = [];
        if ($filter = $this->admin->getFilterParameters()) {
            $parameters['filter'] = $filter;
        }
        $backUrl = $this->admin->generateUrl('list', $parameters);
        /** @var AbstractOnboardingEntity $object */
        return $this->renderAskQuestion($request, $inquiryManager, $object, $formAction, $backUrl);
    }

    /**
     * Renders a view while passing mandatory parameters on to the template.
     *
     * @param string               $view       The view name
     * @param array<string, mixed> $parameters An array of parameters to pass to the view
     *
     * @return Response A Response instance
     */
    public function renderWithExtraParams($view, array $parameters = [], ?Response $response = null)
    {
        if ($parameters['action'] === 'edit' && $parameters['object'] instanceof AbstractOnboardingEntity) {
            $parameters['objectCompletenessInfo'] = $this->onboardingManager->getCompletionInfo($parameters['object']);
        }
        return parent::renderWithExtraParams($view, $parameters, $response);
    }
}
