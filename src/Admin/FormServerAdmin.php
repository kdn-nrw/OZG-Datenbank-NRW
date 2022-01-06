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

use App\Entity\FormServer;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;


class FormServerAdmin extends AbstractAppAdmin implements EnableFullTextSearchAdminInterface
{
    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.form_server.entity.form_server_solutions_solution' => 'app.form_server.entity.form_server_solutions',
    ];

    protected function configureTabMenu(ItemInterface $menu, $action, ?AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, ['edit', 'show'])) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        if (null !== $admin) {
            $id = $admin->getRequest()->get('id');

            $menu->addChild('app.form_server.actions.show', [
                'uri' => $admin->generateUrl('show', ['id' => $id])
            ]);

            if ($this->isGranted('EDIT')) {
                $menu->addChild('app.form_server.actions.edit', [
                    'uri' => $admin->generateUrl('edit', ['id' => $id])
                ]);
            }

            if ($this->isGranted('LIST')) {
                $menu->addChild('app.form_server.actions.solutions_list', [
                    'uri' => $admin->getChild(FormServerSolutionAdmin::class)->generateUrl('list')
                ]);
            }
        }
    }

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->with('app.form_server.tabs.general', ['tab' => true])
                ->with('general', [
                    'label' => false,
                ])
                    ->add('name', TextType::class)
                    ->add('url', UrlType::class, [
                        'required' => false,
                    ])
                ->end()
            ->end()
            ->tab('app.form_server.tabs.solutions')
                ->with('form_server_solutions', [
                    'label' => false,
                ]);
        /** @var FormServer $subject */
        $subject = $this->getSubject();
        if (null !== $subject && $subject->getId() && $subject->getFormServerSolutions()->count() > 50) {
            $form
                ->add('formServerSolutions', ModelType::class, [
                    'label' => false,
                    'btn_add' => false,
                    'placeholder' => '',
                    'required' => false,
                    'multiple' => true,
                    'by_reference' => false,
                    'choice_translation_domain' => false,
                ]);
        } else {
            $form
                ->add('formServerSolutions', CollectionType::class, [
                    'label' => false,
                    'type_options' => [
                        'delete' => true,
                    ],
                    'by_reference' => false,
                ], [
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'position',
                    'ba_custom_exclude_fields' => ['formServer'],
                ]);
        }
        $form
                ->end()
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter->add('name');
        $this->addDefaultDatagridFilter($filter, 'formServerSolutions.solution');
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
            ->add('url', 'url')
            ->add('formServerSolutions', null, [
                'associated_property' => 'solution'
            ]);
    }
}
