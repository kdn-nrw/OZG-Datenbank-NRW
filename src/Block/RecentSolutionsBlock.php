<?php
declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Block;

use App\Entity\Manager\SolutionManager;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\Doctrine\Model\ManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;


class RecentSolutionsBlock extends AbstractBlockService
{
    /**
     * @var SolutionManager
     */
    protected $manager;
    /**
     * @var Pool
     */
    protected ?Pool $adminPool;

    /**
     * @param Environment $twig
     * @param ManagerInterface $manager
     * @param Pool|null $adminPool
     */
    public function __construct(Environment $twig, ManagerInterface $manager, Pool $adminPool = null)
    {
        $this->manager = $manager;
        $this->adminPool = $adminPool;
        parent::__construct($twig);
    }

    /**
     * Creates the block content
     *
     * @param BlockContextInterface $blockContext
     * @param Response|null $response
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $mode = (string) $blockContext->getSetting('mode');
        $isAdminMode = 'admin' === $mode;
        $admin = $this->adminPool->getAdminByAdminCode($blockContext->getSetting('code'));
        $parameters = [
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'results' => $this->manager->findLatest(!$isAdminMode, (int) $blockContext->getSetting('number')),
            'admin_pool' => $this->adminPool,
            'admin' => $admin,
            'isAdminMode' => $isAdminMode,
        ];
        $response = $this->renderResponse($blockContext->getTemplate(), $parameters, $response);
        if ($isAdminMode) {
            $response
                ->setTtl(0)
                ->setPrivate();
        }
        return $response;
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'number' => 5,
            'mode' => 'public',
            'title' => null,
            'translation_domain' => null,
            'icon' => 'fas fa-comments',
            'class' => null,
            'code' => false,
            'code_public' => false,
            'template' => 'Block/recent-solutions.html.twig',
        ]);
    }
}