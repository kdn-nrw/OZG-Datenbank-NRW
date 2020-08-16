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

use App\Admin\BureauAdmin;
use App\Admin\FundingAdmin;
use App\Admin\OrganisationAdmin;
use App\Admin\PortalAdmin;
use App\Datagrid\CustomDatagrid;
use App\Entity\ImplementationStatus;
use App\Entity\Subject;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;


class ImplementationProjectAdmin extends AbstractFrontendAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->addFullTextDatagridFilter($datagridMapper);
        $datagridMapper->add('name');
        $datagridMapper->add('solutions',
            null, [
                'admin_code' => SolutionAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSystems',
            null,
            [
                'admin_code' => ServiceSystemAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('serviceSystems.situation.subject',
            null,
            ['label' => 'app.situation.entity.subject'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('status');
        $datagridMapper->add('projectStartAt');
        $datagridMapper->add('fundings',
            null, [
                'admin_code' => FundingAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('services.bureaus',
            null,
            ['label' => 'app.implementation_project.entity.bureaus'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('services.portals',
            null,
            ['label' => 'app.implementation_project.entity.portals'],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('interestedOrganisations',
            null, [
                'admin_code' => OrganisationAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('participationOrganisations',
            null, [
                'admin_code' => OrganisationAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
        $datagridMapper->add('projectLeaders',
            null, [
                'admin_code' => OrganisationAdmin::class,
            ],
            null,
            ['expanded' => false, 'multiple' => true]
        );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('serviceSystems.situation.subject', 'string', [
                'label' => 'app.situation.entity.subject',
                //'associated_property' => 'name',
                'template' => 'ImplementationProjectAdmin/list-service-system-subjects.html.twig',
            ])
            ->add('status', 'choice', [
                'editable' => true,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
            ])
            ->add('projectStartAt', null, [
                // https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/classSimpleDateFormat.html#details
                'pattern' => 'MMMM yyyy',
            ]);
        $this->addDefaultListActions($listMapper);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('publishedSolutions', null, [
                'admin_code' => SolutionAdmin::class,
                'route' => [
                    'name' => 'show',
                ],
            ])
            ->add('serviceSystems', null, [
                'admin_code' => ServiceSystemAdmin::class,
                'route' => [
                    'name' => 'show',
                ],
            ])
            ->add('services', null, [
                'admin_code' => ServiceAdmin::class,
                'template' => 'General/Show/show-services.html.twig',
                'showFimTypes' => true,
                'route' => [
                    'name' => 'show',
                ],
            ])
            ->add('description')
            ->add('status', 'choice', [
                'editable' => false,
                'class' => ImplementationStatus::class,
                'catalogue' => 'messages',
            ])
            ->add('projectStartAt', null, [
                // https://unicode-org.github.io/icu-docs/apidoc/released/icu4c/classSimpleDateFormat.html#details
                'pattern' => 'MMMM yyyy',
            ])
            ->add('interestedOrganisations', null, [
                'route' => [
                    'name' => 'show',
                ],
            ])
            ->add('participationOrganisations', null, [
                'route' => [
                    'name' => 'show',
                ],
            ])
            ->add('projectLeaders', null, [
                'route' => [
                    'name' => 'show',
                ],
            ])
            ->add('notes', 'html');
        $showMapper
            ->add('fundings', null, [
                'admin_code' => FundingAdmin::class,
            ]);
        $showMapper->add('bureaus', null, [
            'admin_code' => BureauAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-services-bureaus.html.twig',
        ]);
        $showMapper->add('portals', null, [
            'admin_code' => PortalAdmin::class,
            'template' => 'ImplementationProjectAdmin/show-services-portals.html.twig',
        ]);
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
        $datagrid->addFilterMenu('serviceSystems.situation.subject', $subjects, 'app.situation.entity.subject', Subject::class);
    }


    protected function getRoutePrefix(): string
    {
        return 'frontend_app_implementationproject';
    }
}
