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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class ApiConsumerAdmin extends AbstractAppAdmin
{
    use InjectApiManagerTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class, [
                'required' => true
            ])
            ->add('url', UrlType::class, [
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('proxy', UrlType::class, [
                'required' => false
            ])
            ->add('consumerKey', ChoiceType::class, [
                'choices' => $this->apiManager->getConsumerChoices(),
                'required' => true,
            ]);
        $formMapper->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
        $listMapper->add('url', 'url');
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('description')
            ->add('url', 'url');
    }
}
