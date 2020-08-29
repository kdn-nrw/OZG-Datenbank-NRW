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

use App\Admin\Traits\ApplicationCategoryTrait;
use App\Admin\Traits\CommuneTrait;
use App\Admin\Traits\ManufaturerTrait;
use App\Admin\Traits\ServiceProviderTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;
use Sonata\FormatterBundle\Form\Type\SimpleFormatterType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class ApplicationAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    use ApplicationCategoryTrait;
    use CommuneTrait;
    use ManufaturerTrait;
    use ServiceProviderTrait;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', TextType::class);
        $this->addManufaturersFormFields($formMapper);
        $this->addApplicationCategoriesFormFields($formMapper);
        $formMapper
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $this->addCommunesFormFields($formMapper);
        $this->addServiceProvidersFormFields($formMapper);
        $formMapper
            ->add('accessibility', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ])
            ->add('privacy', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ])
            ->add('archive', SimpleFormatterType::class, [
                'format' => 'richhtml',
                'ckeditor_context' => 'default', // optional
            ])
            ->add('inHouseDevelopment', BooleanType::class, [
                'required' => false,
                // the transform option enable compatibility with the boolean field (default 1=true, 2=false)
                // with transform set to true 0=false, 1=true
                'transform' => true,
            ]);
        $formMapper->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
        $this->addManufaturersDatagridFilters($datagridMapper);
        $this->addApplicationCategoriesDatagridFilters($datagridMapper);
        $this->addCommunesDatagridFilters($datagridMapper);
        $this->addServiceProvidersDatagridFilters($datagridMapper);
        $datagridMapper->add('inHouseDevelopment');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name');
        $this->addManufaturersListFields($listMapper);
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
        $this->addApplicationCategoriesShowFields($showMapper);
        $this->addManufaturersShowFields($showMapper);
        $this->addCommunesShowFields($showMapper);
        $this->addServiceProvidersShowFields($showMapper);
        $showMapper
            ->add('accessibility', 'html')
            ->add('privacy', 'html')
            ->add('archive', 'html')
            ->add('inHouseDevelopment');
    }
}
