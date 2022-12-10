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

use App\Statistics\AbstractChartJsStatisticsProvider;
use App\Statistics\ProviderLoader;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\Form\Type\ImmutableArrayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;


class StatisticsBlock extends AbstractBlockService
{
    /**
     * @var ProviderLoader
     */
    protected ProviderLoader $providerLoader;
    /**
     * @var Pool
     */
    protected ?Pool $adminPool;

    /**
     * @param Environment $twig
     * @param ProviderLoader $providerLoader
     * @param Pool|null $adminPool
     */
    public function __construct(Environment $twig, ProviderLoader $providerLoader, ?Pool $adminPool = null)
    {
        $this->providerLoader = $providerLoader;
        $this->adminPool = $adminPool;
        parent::__construct($twig);
    }

    /**
     * Create block content
     *
     * @param BlockContextInterface $blockContext
     * @param Response|null $response
     * @return Response
     */
    public function execute(BlockContextInterface $blockContext, ?Response $response = null): Response
    {
        $mode = (string) $blockContext->getSetting('mode');
        $isAdminMode = 'admin' === $mode;
        /** @var AbstractChartJsStatisticsProvider $provider */
        $provider = $this->providerLoader->getProviderByKey($blockContext->getSetting('provider'));
        $parameters = [
            'context' => $blockContext,
            'settings' => $blockContext->getSettings(),
            'block' => $blockContext->getBlock(),
            'admin_pool' => $this->adminPool,
            //'provider' => $provider,
            'chartKey' => $provider->getKey()
        ];
        $response = $this->renderResponse($blockContext->getTemplate(), $parameters, $response);
        if ($isAdminMode) {
            $response
                ->setTtl(0)
                ->setPrivate();
        }
        return $response;
    }

    public function buildEditForm(FormMapper $form, BlockInterface $block)
    {
        $form->add('settings', ImmutableArrayType::class, [
            'keys' => [
                ['number', IntegerType::class, [
                    'required' => true,
                    'label' => 'form.label_number',
                ]],
                ['title', TextType::class, [
                    'label' => 'form.label_title',
                    'required' => false,
                ]],
                ['translation_domain', TextType::class, [
                    'label' => 'form.label_translation_domain',
                    'required' => false,
                ]],
                ['icon', TextType::class, [
                    'label' => 'form.label_icon',
                    'required' => false,
                ]],
                ['class', TextType::class, [
                    'label' => 'form.label_class',
                    'required' => false,
                ]],
                ['provider', TextType::class, [
                    'required' => false,
                ]],
                ['filters', TextType::class, [
                    'required' => false,
                ]],
                ['mode', ChoiceType::class, [
                    'choices' => [
                        'form.label_mode_public' => 'public',
                        'form.label_mode_admin' => 'admin',
                    ],
                    'label' => 'form.label_mode',
                ]],
            ],
            'translation_domain' => 'messages',
        ]);
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'number' => 5,
            'mode' => 'public',
            'title' => null,
            'translation_domain' => null,
            'icon' => 'fas fa-chart-line',
            'class' => null,
            'provider' => null,
            'filters' => null,
            'template' => 'Block/statistics-chart.html.twig',
        ]);
    }
}