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

namespace App\Admin;

use App\Admin\Traits\ColorCodedTrait;
use App\Entity\StatusEntityInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class StatusAdmin extends AbstractAppAdmin
{
    use ColorCodedTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->addDefaultStatusFormFields($formMapper);
        $formMapper
            ->end();
    }

    protected function addDefaultStatusFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name', TextType::class)
            ->add('level', IntegerType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $subject = $this->getSubject();
        if ($subject instanceof StatusEntityInterface) {
            $formMapper
                ->add('prevStatus', ModelType::class, [
                    'label' => 'app.status.entity.prev_status',
                    'btn_add' => false,
                    'required' => false,
                    'choice_translation_domain' => false,
                ])
                ->add('nextStatus', ModelType::class, [
                    'label' => 'app.status.entity.next_status',
                    'btn_add' => false,
                    'required' => false,
                    'choice_translation_domain' => false,
                ]);
        }
        $this->addColorFormFields($formMapper);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->addIdentifier('level');
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('level')
            ->add('description');
    }
}
