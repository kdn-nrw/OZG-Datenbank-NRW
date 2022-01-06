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

namespace App\Admin\Api;


use App\Admin\AbstractAppAdmin;
use App\Api\Consumer\InjectApiManagerTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class ApiConsumerAdmin extends AbstractAppAdmin
{
    use InjectApiManagerTrait;

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('name', TextType::class, [
                'required' => true
            ])
            ->add('url', UrlType::class, [
                'required' => true
            ])
            ->add('description', SimpleFormatterType::class, [
                'required' => false,
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ])
            ->add('proxy', UrlType::class, [
                'required' => false,
                'default_protocol' => null,
            ])
            ->add('consumerKey', ChoiceType::class, [
                'choices' => $this->apiManager->getConsumerChoices(),
                'required' => true,
            ])
            ->add('showInFrontend', BooleanType::class, [
                'required' => false,
                // the transform option enable compatibility with the boolean field (default 1=true, 2=false)
                // with transform set to true 0=false, 1=true
                'transform' => true,
            ]);
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('name');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list->addIdentifier('name');
        $list->add('url', 'url');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('name')
            ->add('description')
            ->add('url', 'url');
    }
}
