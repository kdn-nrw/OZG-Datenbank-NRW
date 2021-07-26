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
use App\Entity\ImplementationProject;
use App\Entity\ImplementationStatus;
use App\Entity\StateGroup\Commune;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Trait CommuneTrait
 * @package App\Admin\Traits
 */
trait CommuneTrait
{
    protected function addCommunesFormFields(FormMapper $formMapper, array $overrideOptions = [])
    {
        $fieldDescriptionOptions = [
            'admin_code' => CommuneAdmin::class,
        ];
        if (!empty($overrideOptions['disabled'])) {
            $options = [
                'class' => Commune::class,
                'multiple' => true,
            ];
            $options = array_merge($options, $overrideOptions);
            $formMapper->add('communes', EntityType::class, $options, $fieldDescriptionOptions);

        } else {
            $options = [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
                'choice_translation_domain' => false,
                'class' => Commune::class,
            ];
            if (!empty($overrideOptions)) {
                $options = array_merge($options, $overrideOptions);
            }
            $formMapper->add('communes', ModelType::class, $options, $fieldDescriptionOptions);
        }
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