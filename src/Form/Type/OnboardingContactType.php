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

namespace App\Form\Type;


use App\Entity\Onboarding\Contact;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class OnboardingContactType
 */
class OnboardingContactType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$parentAdmin = $options['parent_admin'];

        if ($options['enable_external_user']) {
            $builder->add('externalUserName', TextType::class, [
                'label' => 'app.epayment.entity.payment_user_username',
                'required'    => false,
                'attr' => [
                    'placeholder' => 'app.epayment.entity.payment_user_username_placeholder',
                    'maxlength' => 20,
                ],
            ]);
        }
        // Labels are not translated in the form type! Translate manually.
        //$label = $parentAdmin->trans($data->getLabelKey(), [], 'messages');
        if ($options['show_contact_type']) {
            $builder->add('contactType', ChoiceType::class, [
                'label' => 'app.contact.entity.contact_type',
                'choices' => Contact::$contactTypeChoices,
                'disabled' => true,
                'expanded' => false,
                'required' => false,
                'attr' => [
                    'data-sonata-select2' => 'false',
                    'class' => 'form-control form-control-head',
                ],
            ]);
        }
        $builder->add('firstName', TextType::class, [
            'label' => 'app.contact.entity.first_name',
            'required'    => false,
        ]);
        $builder->add('lastName', TextType::class, [
            'label' => 'app.contact.entity.last_name',
            'required'    => false,
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'app.contact.entity.email',
            'constraints' => [new Assert\Email()],
            'required'    => false,
        ]);
        if ($options['enable_phone_number']) {
            $builder->add('phoneNumber', TextType::class, [
                'label' => 'app.contact.entity.phone_number',
                'required' => false,
            ]);
        }
        if ($options['enable_mobile_number']) {
            $builder->add('mobileNumber', TextType::class, [
                'label' => 'app.contact.entity.mobile_number',
                'required'    => false,
            ]);
        }
        /** @var AdminInterface|null $parentAdmin * /
        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($parentAdmin) {
            $data = $event->getData();
            if ($data instanceof Contact) {
                /** @var Contact $data * /
                $form = $event->getForm();
            }
        });*/
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'       => Contact::class,
            'parent_admin' => null,
            'show_contact_type' => true,
            'enable_external_user' => false,
            'enable_mobile_number' => false,
            'enable_phone_number' => true,
        ]);
    }

}
