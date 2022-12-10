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
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class StatusAdmin extends AbstractAppAdmin
{
    use ColorCodedTrait;

    /**
     * Configures a list of default sort values.
     *
     * @phpstan-param array{_page?: int, _per_page?: int, _sort_by?: string, _sort_order?: string} $sortValues
     * @param array $sortValues
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        parent::configureDefaultSortValues($sortValues);
        $sortValues[DatagridInterface::SORT_ORDER] = $sortValues[DatagridInterface::SORT_ORDER] ?? 'ASC';
        $sortValues[DatagridInterface::SORT_BY] = $sortValues[DatagridInterface::SORT_BY] ?? 'level';
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $this->addDefaultStatusFormFields($form);
        $form
            ->end();
    }

    protected function addDefaultStatusFormFields(FormMapper $form): void
    {
        $form
            ->add('name', TextType::class)
            ->add('level', IntegerType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $subject = $this->getSubject();
        if ($subject instanceof StatusEntityInterface) {
            $form
                ->add('prevStatus', EntityType::class, [
                    'label' => 'app.status.entity.prev_status',
                    'class' => $this->getClass(),
                    'required' => false,
                    'choice_translation_domain' => false,
                    'placeholder' => 'app.status.entity.prev_status_placeholder',
                    'empty_data' => null,
                ])
                ->add('nextStatus', EntityType::class, [
                    'label' => 'app.status.entity.next_status',
                    'class' => $this->getClass(),
                    'required' => false,
                    'choice_translation_domain' => false,
                    'placeholder' => 'app.status.entity.next_status_placeholder',
                    'empty_data' => null,
                ]);
        }
        $this->addColorFormFields($form);
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('name');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('name');
        if (is_a($this->getClass(), StatusEntityInterface::class, true)) {
            $list->add('nextStatus', null, [
                'label' => 'app.status.entity.next_status',
            ]);
        }
        $list
            ->add('level');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('name')
            ->add('level')
            ->add('description');
    }
}
