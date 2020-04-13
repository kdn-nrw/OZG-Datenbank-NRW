<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-04-13
 */

namespace App\Controller;


use App\Admin\SearchableAdminInterface;
use App\Entity\Search;
use App\Form\Type\SearchType;
use App\Statistics\AbstractChartJsStatisticsProvider;
use App\Statistics\ChartStatisticsProviderInterface;
use App\Statistics\ProviderLoader;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\DoctrineORMAdminBundle\Filter\Filter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class StatisticsController
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 * @since     2020-04-13
 */
class StatisticsController extends AbstractController
{
    /**
     * @var Pool
     */
    private $adminPool;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var ProviderLoader
     */
    private $providerLoader;

    /**
     * SearchController constructor.
     * @param Pool $adminPool
     * @param ProviderLoader $providerLoader
     * @param TranslatorInterface $translator
     */
    public function __construct(Pool $adminPool, ProviderLoader $providerLoader, TranslatorInterface $translator)
    {
        $this->adminPool = $adminPool;
        $this->translator = $translator;
        $this->providerLoader = $providerLoader;
    }


    /**
     * @param string $key
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function chartAction(string $key)
    {
        $provider = $this->providerLoader->getProviderByKey($key);
        if ($provider instanceof ChartStatisticsProviderInterface) {
            /** @var ChartStatisticsProviderInterface $provider */
            $chartConfig = $provider->getChartConfig();
            $jsonData = [
                'type' => 'chart',
                'key' => $key,
                'chartConfig' => $chartConfig,
                'status' => 1,
            ];
        } else {
            $jsonData = [
                'type' => 'error',
                'key' => $key,
                'status' => -1,
            ];
        }
        return $this->json($jsonData);
    }
}
