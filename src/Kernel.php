<?php

namespace App;

use App\Api\Consumer\ApiConsumerInterface;
use App\DependencyInjection\Compiler\AddAuditEntityCompilerPass;
use App\DependencyInjection\Compiler\AdminTranslatorStrategyCompilerPass;
use App\DependencyInjection\Compiler\ApiManagerCompilerPass;
use App\DependencyInjection\Compiler\ChartStatisticsCompilerPass;
use App\DependencyInjection\Compiler\CustomFieldAdminCompilerPass;
use App\Statistics\ChartStatisticsProviderInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * The extension point similar to the Bundle::build() method.
     *
     * Use this method to register compiler passes and manipulate the container during the building process.
     */
    protected function build(ContainerBuilder $container)
    {
        // https://symfony.com/doc/4.4/service_container/tags.html#autoconfiguring-tags
        $container->registerForAutoconfiguration(ApiConsumerInterface::class)
            ->addTag('app.api_consumer');
        $container->registerForAutoconfiguration(ChartStatisticsProviderInterface::class)
            ->addTag('custom_statistics.provider');
        // https://symfony.com/doc/4.4/service_container/tags.html#create-a-compiler-pass
        $container->addCompilerPass(new AddAuditEntityCompilerPass());
        $container->addCompilerPass(new ChartStatisticsCompilerPass());
        $container->addCompilerPass(new ApiManagerCompilerPass());
        $container->addCompilerPass(new CustomFieldAdminCompilerPass());
        $container->addCompilerPass(new AdminTranslatorStrategyCompilerPass());
    }
}
