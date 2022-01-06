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

namespace App\Admin;

use App\Entity\PageContent;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class PageContentAdmin extends AbstractAppAdmin
{
    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('page', ChoiceType::class, [
                'choices' => array_flip(PageContent::$pageChoices),
                'required' => true,
            ])
            ->add('headline', TextType::class, [
                'required' => false,
            ])
            ->add('bodytext', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
                'required' => false,
            ])
            ->add('position', IntegerType::class);
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('page');
        $filter->add('headline');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->add('createdAt')
            ->add('page', 'choice', [
                'editable' => true,
                'choices' => PageContent::$pageChoices,
                'catalogue' => 'messages',
            ])
            ->add('headline')
            ->add('position');
        $this->addDefaultListActions($list);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show
            ->add('page')
            ->add('headline')
            ->add('bodytext');
    }
}
