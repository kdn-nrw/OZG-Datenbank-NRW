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

namespace App\Admin\StateGroup;


use App\Admin\AbstractAppAdmin;
use App\Admin\EnableFullTextSearchAdminInterface;
use App\Admin\Traits\ServiceSystemTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BureauAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    protected $baseRoutePattern = 'state/bureau';

    use ServiceSystemTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', TextType::class);
        $this->addServiceSystemsFormFields($formMapper);
        $formMapper
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $formMapper->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSystems');
        $this->addDefaultDatagridFilter($datagridMapper, 'serviceSystems.situation.subject');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name');
        $this->addServiceSystemsListFields($listMapper);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper->add('name')
            ->add('description');
        $this->addServiceSystemsShowFields($showMapper);
    }
}
