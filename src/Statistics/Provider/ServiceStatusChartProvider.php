<?php
namespace App\Statistics\Provider;

use App\Entity\Manager\ServiceManager;
use App\Entity\Service;
use App\Statistics\AbstractChartJsStatisticsProvider;
use Doctrine\DBAL\Connection;

class ServiceStatusChartProvider extends AbstractChartJsStatisticsProvider {

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * Provider type (excel|csv|chart)
     * @var string
     */
    protected $type = 'chart';

    protected $chartType = 'bar';

    /**
     * @required
     * @param ServiceManager $serviceManager
     */
    public function injectServiceManager(ServiceManager $serviceManager): void
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @inheritDoc
     */
    protected function createChartData()
    {
        $groupedData = $this->loadData();
        $xAxisLabels = array_keys($groupedData);

        $dataSetConfiguration = [];
        $offset = 0;
        $dataSetConfiguration[$offset] = [
            'label' => 'Anzahl der Leika-Leistungen',
            'backgroundColor' => $this->colors,
            'data' => [],
        ];
        foreach ($groupedData as $key => $data) {
            $dataSetConfiguration[$offset]['data'][] = $data;
        }
        $chartData = [
            'labels' => array_values($xAxisLabels),
            'datasets' => $dataSetConfiguration,
        ];
        return $chartData;
    }

    /**
     * @inheritDoc
     */
    protected function loadData()
    {
        /** @var Connection $connection */
        $queryBuilder = $this->serviceManager->getEntityManager()->createQueryBuilder();
        $queryBuilder
            ->from(Service::class, 's')
            ->select('s.id', 'IDENTITY(s.status) AS statusId', 'st.name')
            ->leftJoin('s.status', 'st')
            ->orderBy('s.status')
            ->addOrderBy('s.id');
        //$queryBuilder->
        $query = $queryBuilder->getQuery();
        $result = $query->getArrayResult();
        $data = [];
        foreach ($result as $row) {
            $key = (string) $row['name'] !== '' ? $row['name'] : 'n.a';
            if (!isset($data[$key])) {
                $data[$key] = 0;
            }
            ++$data[$key];
        }
        return $data;
    }
}