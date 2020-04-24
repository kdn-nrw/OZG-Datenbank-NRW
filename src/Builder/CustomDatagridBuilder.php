<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Builder;


use App\Datagrid\CustomDatagrid;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\Datagrid;
use Sonata\DoctrineORMAdminBundle\Builder\DatagridBuilder;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * Class CustomDatagridBuilder
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-09
 */
class CustomDatagridBuilder extends DatagridBuilder
{

    public function getBaseDatagrid(AdminInterface $admin, array $values = [])
    {
        $pager = $this->getPager($admin->getPagerType());

        $pager->setCountColumn($admin->getModelManager()->getIdentifierFieldNames($admin->getClass()));

        $defaultOptions = [];
        if ($this->csrfTokenEnabled) {
            $defaultOptions['csrf_protection'] = false;
        }

        $formBuilder = $this->formFactory->createNamedBuilder('filter', FormType::class, [], $defaultOptions);

        return new CustomDatagrid($admin->createQuery(), $admin->getList(), $pager, $formBuilder, $values);
    }

}