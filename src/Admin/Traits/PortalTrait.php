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

use App\Admin\PortalAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;

trait PortalTrait
{
    protected function addPortalsFormFields(FormMapper $formMapper)
    {
        $formMapper->add('portals', ModelType::class, [
            'btn_add' => false,
            'placeholder' => '',
            'required' => false,
            'multiple' => true,
            'by_reference' => false,
            'choice_translation_domain' => false,
        ]);
    }

    protected function addPortalsListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('portals', null,[
                'admin_code' => PortalAdmin::class,
            ]);
    }

    /**
     * @inheritdoc
     */
    public function addPortalsShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('portals', null,[
                'admin_code' => PortalAdmin::class,
            ]);
    }
}