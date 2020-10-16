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

namespace App\Admin\StateGroup;

use App\Admin\AbstractAppAdmin;
use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ServiceSystemTrait;
use App\Entity\ServiceSystem;
use App\Entity\StateGroup\CommuneType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class CommuneTypeAdmin extends AbstractAppAdmin
{
    use CommuneTrait;
    use ServiceSystemTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('constituency', BooleanType::class, [
                'required' => false,
                // the transform option enable compatibility with the boolean field (default 1=true, 2=false)
                // with transform set to true 0=false, 1=true
                'transform' => true,
            ]);
        $this->addCommunesFormFields($formMapper);
        $this->addServiceSystemsFormFields($formMapper);
        $formMapper->end();
    }

    public function preUpdate($object)
    {
        /** @var CommuneType $object */
        $serviceSystems = $object->getServiceSystems();
        foreach ($serviceSystems as $serviceSystem) {
            /** @var ServiceSystem $serviceSystem */
            $serviceSystem->saveInheritedValues();
        }
    }

    public function prePersist($object)
    {
        /** @var CommuneType $object */
        $serviceSystems = $object->getServiceSystems();
        foreach ($serviceSystems as $serviceSystem) {
            /** @var ServiceSystem $serviceSystem */
            $serviceSystem->saveInheritedValues();
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addCommunesDatagridFilters($datagridMapper);
        $this->addServiceSystemsDatagridFilters($datagridMapper);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name');
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('description');
        $this->addCommunesShowFields($showMapper);
        $this->addServiceSystemsShowFields($showMapper);
    }
}
