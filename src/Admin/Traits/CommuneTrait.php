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

namespace App\Admin\Traits;

use App\Admin\StateGroup\CommuneAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Trait CommuneTrait
 * @package App\Admin\Traits
 */
trait CommuneTrait
{
    protected function addCommunesFormFields(FormMapper $formMapper)
    {
        $formMapper->add('communes', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
    }

    protected function addCommunesDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('communes',
            null, [
                'admin_code' => CommuneAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function addCommunesListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('communes', null,[
                'admin_code' => CommuneAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addCommunesShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('communes', null,[
                'associated_property' => 'name',
                'admin_code' => CommuneAdmin::class,
                'check_has_all_modifier' => false,
            ]);
    }
}