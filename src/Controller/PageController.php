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


use App\Entity\PageContent;
use App\Service\InjectPageContentManagerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PageController
 */
class PageController extends AbstractController
{
    use InjectPageContentManagerTrait;

    public function onboardingDvdvAction()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->renderPageContent(PageContent::PAGE_ONBOARDING_DVDV);
        }
        return $this->redirectToRoute('frontend_app_service_list');
    }

    protected function renderPageContent(int $pageKey): Response
    {
        return $this->render('PageContent/admin-page_content.html.twig', [
            'pageKey' => $pageKey,
        ]);
    }
}
