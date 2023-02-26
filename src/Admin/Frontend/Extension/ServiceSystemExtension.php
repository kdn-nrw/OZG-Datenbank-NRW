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
use App\Entity\Subject;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

/**
 * Frontend admin extension for custom datagrid filters
 */
class ServiceSystemExtension extends AbstractAdminExtension
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
            if ($datagrid && !$datagrid->hasFilterMenu('situation.subject')) {
                $modelManager = $admin->getModelManager();
                //$situations = $modelManager->findBy(Situation::class);
                //$datagrid->addFilterMenu('serviceSystem.situation', $situations, 'app.service_system.entity.situation');
                $subjects = $modelManager->findBy(Subject::class);
                $datagrid->addFilterMenu('situation.subject', $subjects, 'app.situation.entity.subject', Subject::class);
            }
        }
    }
}
