<?php
declare(strict_types=1);

namespace App\Block;

use App\Entity\Manager\SolutionManager;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Meta\Metadata;
use Sonata\Doctrine\Model\ManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Templating\EngineInterface;


class RecentSolutionsBlock extends AbstractBlockService
{
    /**
     * @var SolutionManager
     */
    protected $manager;
    /**
     * @var Pool
     */
    protected $adminPool;

    /**
     * @param EngineInterface $templating
     * @param ManagerInterface $commentManager
     * @param Pool $adminPool
     */
    public function __construct(EngineInterface $templating, ManagerInterface $commentManager, Pool $adminPool = null)
    {
        $this->manager = $commentManager;
        $this->adminPool = $adminPool;
        parent::__construct($templating);
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $mode = $blockContext->getSetting('mode');
        $isAdminMode = 'admin' === $blockContext->getSetting('mode');
        $codeKey = $isAdminMode ? 'code' : 'code_public';
        $admin = $this->adminPool->getAdminByAdminCode($blockContext->getSetting($codeKey));
        $criteria = [
            'mode' => $mode,
        ];
        $parameters = [
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'pager' => $this->manager->getPager($criteria, 1, $blockContext->getSetting('number')),
            'admin_pool' => $this->adminPool,
            'admin' => $admin,
            'isAdminMode' => $isAdminMode,
        ];
        if ('admin' === $blockContext->getSetting('mode')) {
            return $this->renderPrivateResponse($blockContext->getTemplate(), $parameters, $response);
        }
        return $this->renderResponse($blockContext->getTemplate(), $parameters, $response);
    }

    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'number' => 5,
            'mode' => 'public',
            'title' => null,
            'translation_domain' => null,
            'icon' => 'fa fa-comments',
            'class' => null,
            'code' => false,
            'code_public' => false,
            'disable_public' => false,
            'template' => 'Block/recent-solutions.html.twig',
        ]);
    }

    public function getBlockMetadata($code = null)
    {
        return new Metadata($this->getName(), (null !== $code ? $code : $this->getName()), false, 'messages', [
            'class' => 'fa fa-comments-o',
        ]);
    }
}