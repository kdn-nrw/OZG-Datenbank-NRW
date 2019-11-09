<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */

namespace App\Admin;


use App\Entity\Base\NamedEntityInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class AbstractAppAdmin
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */
abstract class AbstractAppAdmin extends AbstractAdmin
{
    use AdminTranslatorStrategyTrait;


    protected function configureFormFields(FormMapper $formMapper)
    {
        if (is_subclass_of($this->getClass(), NamedEntityInterface::class)) {
            $formMapper->add('name', TextType::class);
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if (is_subclass_of($this->getClass(), NamedEntityInterface::class)) {
            $datagridMapper->add('name');
        }
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        if (is_subclass_of($this->getClass(), NamedEntityInterface::class)) {
            $listMapper->addIdentifier('name');
        };
        $listMapper
            ->add('_action', null, [
                'label' => 'app.common.actions',
                'translation_domain' => 'messages',
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        if (is_subclass_of($this->getClass(), NamedEntityInterface::class)) {
            $showMapper
                ->add('name');
        };
    }

}
