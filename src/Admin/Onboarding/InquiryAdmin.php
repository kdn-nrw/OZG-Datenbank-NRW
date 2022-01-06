<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Admin\Onboarding;


use App\Admin\AbstractAppAdmin;
use App\DependencyInjection\InjectionTraits\InjectSecurityTrait;
use App\Entity\Onboarding\Inquiry;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class InquiryAdmin extends AbstractAppAdmin
{
    use InjectSecurityTrait;

    protected function configureFormFields(FormMapper $form)
    {
        $form
            ->add('user', ModelType::class, [
                'btn_add' => false,
                'placeholder' => '',
                'required' => false,
                'choice_translation_domain' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ]);
        $subject = $this->getSubject();
        /** @var Inquiry|null $subject */
        if (null !== $subject && $subject->getId() > 0
            && ($this->isGranted('ALL') || $subject->getUser() === $this->security->getUser())) {
            $form->add('isRead', CheckboxType::class, [
                'required' => false,
            ]);
        }
        $form->end();
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $this->addDefaultDatagridFilter($filter, 'user');
        $this->addDefaultDatagridFilter($filter, 'createdBy');
    }

    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('user')
            ->add('createdBy')
            ->add('createdAt');
        $securityHandler = $this->getSecurityHandler();
        if (null !== $securityHandler) {
            $extraActions = [
                'askQuestion' => [
                    'template' => 'Onboarding/Inquiry/action_create_answer.html.twig',
                    'icon' => 'fa-question-circle-o',
                    'route' => 'askQuestion',
                    //'permission' => sprintf($baseRole, 'LIST')
                ],
            ];
        } else {
            $extraActions = null;
        }
        $this->addDefaultListActions($list, $extraActions);
    }

    /**
     * @inheritdoc
     */
    public function configureShowFields(ShowMapper $show)
    {
        $show->add('user')
            ->add('createdBy')
            ->add('createdAt')
            ->add('description')
            ->add('readAt');
    }

    public function getAccessMapping()
    {
        if (!array_key_exists('askQuestion', $this->accessMapping)) {
            $this->accessMapping['askQuestion'] = 'CREATE';
        }
        return parent::getAccessMapping();
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->remove('create');
        $collection
            ->add('askQuestion', $this->getRouterIdParameter() . '/ask-question');
    }

    public function hasRoute($name)
    {
        return $name === 'askQuestion' || parent::hasRoute($name);
    }

    /**
     * @inheritDoc
     */
    public function hasAccess($action, $object = null)
    {
        $hasAccess = parent::hasAccess($action);
        // Only allow edit and delete for creator + only if message has no answers
        if ($hasAccess && $object instanceof Inquiry
            && in_array($action, ['edit', 'delete'], false)
            && !$this->isGranted('ALL')) {
            return $object->getCreatedBy() === $this->security->getUser() && $object->getAnswers()->count() === 0;
        }
        return $hasAccess;
    }
}
