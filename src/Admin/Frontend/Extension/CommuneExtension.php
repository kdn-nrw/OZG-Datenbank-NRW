<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2023 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Admin\Frontend\Extension;

use App\Builder\CustomDatagridBuilder;
use App\Datagrid\CustomDatagrid;
use App\Entity\StateGroup\AdministrativeDistrict;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

/**
 * Frontend admin extension for custom datagrid filters
 */
class CommuneExtension extends AbstractAdminExtension
{

    /**
     * @phpstan-param DatagridMapper $filter
     */
    public function configureDatagridFilters(DatagridMapper $filter): void
    {
        $admin = $filter->getAdmin();
        if (($builder = $admin->getDatagridBuilder()) instanceof CustomDatagridBuilder) {
            /** @var CustomDatagridBuilder $builder */
            $datagrid = $builder->getCustomDatagrid();
            if ($datagrid && !$datagrid->hasFilterMenu('administrativeDistrict')) {
                /** @var CustomDatagrid $datagrid */
                $modelManager = $admin->getModelManager();
                //$situations = $modelManager->findBy(Situation::class);
                //$datagrid->addFilterMenu('serviceSystem.situation', $situations, 'app.service_system.entity.situation');
                $filterChoices = $modelManager->findBy(AdministrativeDistrict::class);
                $datagrid->addFilterMenu(
                    'administrativeDistrict',
                    $filterChoices,
                    'app.commune.entity.administrative_district_placeholder',
                    AdministrativeDistrict::class
                );
            }
        }
    }
}
