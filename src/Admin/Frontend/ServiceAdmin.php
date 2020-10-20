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

namespace App\Admin\Frontend;

use App\Admin\EnableFullTextSearchAdminInterface;
use App\Admin\LaboratoryAdmin;
use App\Admin\PortalAdmin;
use App\Admin\StateGroup\CommuneTypeAdmin;
use App\Datagrid\CustomDatagrid;
use App\Entity\FederalInformationManagementType;
use App\Entity\Priority;
use App\Entity\Status;
use App\Entity\Subject;
use App\Exporter\Source\ServiceFimValueFormatter;
use App\Model\ExportSettings;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ServiceAdmin extends AbstractFrontendAdmin implements EnableFullTextSearchAdminInterface
{

    /**
     * @var string[]
     */
    protected $customLabels = [
        'app.service.entity.service_system_situation' => 'app.service_system.entity.situation',
        'app.service.entity.service_system_situation_subject' => 'app.situation.entity.subject',
        'app.service.entity.service_system_service_key' => 'app.service_system.entity.service_key',
        'app.service.entity.service_system_priority' => 'app.service_system.entity.priority',
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('serviceSystem.situation.subject',
            null,
            [
                'show_filter' => true,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSystem.situation',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('name');
        $datagridMapper->add('serviceKey');
        $datagridMapper->add('serviceType');
        $datagridMapper->add('serviceSystem',
            null,
            [
                'admin_code' => ServiceSystemAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSystem.serviceKey');
        $datagridMapper->add('status');
        $datagridMapper->add('laboratories',
            null, [
                'admin_code' => LaboratoryAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('jurisdictions',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('bureaus',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('portals',
            null, [
                'admin_code' => PortalAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('ruleAuthorities',
            null,
            [],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('fimTypes.dataType',
            null, [
            ],
            ChoiceType::class,
            [
                'choices' => array_flip(FederalInformationManagementType::$mapTypes)
            ]
        );
        $datagridMapper->add('fimTypes.status',
            null, [
            ],
            ChoiceType::class,
            [
                'choices' => array_flip(FederalInformationManagementType::$statusChoices)
            ]
        );
        $datagridMapper->add('communeTypes',
            null, [
                'admin_code' => CommuneTypeAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('serviceSystem.situation.subject', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSystem'],
                    ['fieldName' => 'situation'],
                    ['fieldName' => 'subject'],
                ]
            ])
            ->add('serviceSystem.situation', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                // https://stackoverflow.com/questions/36153381/sort-list-view-in-sonata-admin-by-related-entity-fields
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSystem'],
                    ['fieldName' => 'situation'],
                ]
            ])
            ->add('serviceSystem', null, [
                'admin_code' => ServiceSystemAdmin::class,
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'name'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSystem'],
                ],
                'route' => [
                    'name' => 'show',
                    'parameters' => [],
                ],
            ])
            ->add('serviceSystem.serviceKey', null, [
                'sortable' => true, // IMPORTANT! make the column sortable
                'sort_field_mapping' => [
                    'fieldName' => 'serviceKey'
                ],
                'sort_parent_association_mappings' => [
                    ['fieldName' => 'serviceSystem'],
                ]
            ])
            ->add('name')
            ->add('serviceKey');
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritDoc
     */
    public function getExportSettings(): ExportSettings
    {
        $settings = parent::getExportSettings();
        $settings->addExcludeFields(['fimTypes']);

        $additionalFields = [
            'serviceSystem.situation.subject', 'serviceSystem.situation', 'serviceSystem', 'serviceSystem.serviceKey',
            'name', 'serviceKey', 'serviceType', 'lawShortcuts', 'relevance1', 'relevance2', 'status'
        ];
        $customServiceFormatter = new ServiceFimValueFormatter();
        $fimStatusTypes = FederalInformationManagementType::$statusChoices;
        $statusTranslations = [];
        foreach ($fimStatusTypes as $status => $labelKey) {
            $statusTranslations[$status] = $this->trans($labelKey);
        }
        $customServiceFormatter->setFimStatusTranslations($statusTranslations);
        $fimTypes = FederalInformationManagementType::$mapTypes;
        foreach ($fimTypes as $type => $labelKey) {
            $typeField = ServiceFimValueFormatter::FIM_DATA_TYPE_PREFIX . $type;
            $statusField = ServiceFimValueFormatter::FIM_STATUS_PREFIX . $type;
            $settings->addCustomLabel($typeField, 'FIM Typ ' . $this->trans($labelKey));
            $settings->addCustomLabel($statusField, 'FIM Status ' . $this->trans($labelKey));
            $additionalFields[] = $typeField;
            $additionalFields[] = $statusField;
            $settings->addCustomPropertyValueFormatter($typeField, $customServiceFormatter);
            $settings->addCustomPropertyValueFormatter($statusField, $customServiceFormatter);
        }
        $settings->setAdditionFields($additionalFields);
        return $settings;
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('serviceKey', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('serviceSystem', null, [
                'admin_code' => ServiceSystemAdmin::class,
                'template' => 'ServiceAdmin/show_many_to_one.html.twig',
            ])
            ->add('serviceType', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('legalBasis', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('laws', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('lawShortcuts', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('relevance1', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('relevance2', null, [
                'template' => 'ServiceAdmin/show_field_inline_label.html.twig',
            ])
            ->add('status', 'choice', [
                'editable' => false,
                'class' => Status::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('serviceSystem.priority', 'choice', [
                'editable' => false,
                'class' => Priority::class,
                'catalogue' => 'messages',
                'template' => 'ServiceAdmin/show_choice.html.twig',
            ])
            ->add('serviceSystem.situation')
            ->add('serviceSystem.situation.subject')
            ->add('serviceSolutions', null, [
                'associated_property' => 'solution'
            ])
            ->add('jurisdictions')
            ->add('bureaus')
            ->add('ruleAuthorities')
            ->add('authorityBureaus')
            ->add('authorityStateMinistries')
            ->add('communeTypes')
            ->add('laboratories')
            ->add('stateMinistries')
            ->add('implementationProjects', null, [
                'admin_code' => ImplementationProjectAdmin::class,
                'route' => [
                    'name' => 'show',
                ],
            ])
            ->add('publishedModelRegionProjects', null, [
                'admin_code' => ModelRegionProjectAdmin::class,
                'route' => [
                    'name' => 'show',
                ],
            ])
            ->add('fimTypes');
    }

    public function isGranted($name, $object = null)
    {
        if (in_array($name, ['LIST', 'VIEW', 'SHOW', 'EXPORT'])) {
            return true;
        }
        return parent::isGranted($name, $object);
    }

    public function buildDatagrid()
    {
        if ($this->datagrid) {
            return;
        }
        parent::buildDatagrid();
        /** @var CustomDatagrid $datagrid */
        $datagrid = $this->datagrid;
        $modelManager = $this->getModelManager();
        //$situations = $modelManager->findBy(Situation::class);
        //$datagrid->addFilterMenu('serviceSystem.situation', $situations, 'app.service_system.entity.situation');
        $subjects = $modelManager->findBy(Subject::class);
        $datagrid->addFilterMenu('serviceSystem.situation.subject', $subjects, 'app.situation.entity.subject', Subject::class);
    }


    protected function getRoutePrefix(): string
    {
        return 'frontend_app_service';
    }
}
