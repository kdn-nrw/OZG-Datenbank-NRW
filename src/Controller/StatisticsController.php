<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;


use App\Statistics\ChartStatisticsProviderInterface;
use App\Statistics\ProviderLoader;
use Sonata\AdminBundle\Admin\Pool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @param Request $request
     * @param string $key The chart key
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function chartAction(Request $request, string $key)
    {
        $provider = $this->providerLoader->getProviderByKey($key);
        if ($provider instanceof ChartStatisticsProviderInterface) {
            if ($request->query->has('filters')) {
                $provider->addFilters($request->query->get('filters'));
            }
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
