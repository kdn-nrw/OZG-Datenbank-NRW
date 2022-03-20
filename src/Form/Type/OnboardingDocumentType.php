<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2022 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Type;

use App\Entity\Onboarding\OnboardingDocument;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class OnboardingDocumentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $parentAdmin = $options['parent_admin'];
        /** @var AbstractAdmin|null $parentAdmin */

        $builder->add('documentType', ChoiceType::class, [
            'label' => 'app.abstract_onboarding_entity.entity.document_type',
            'choices' => OnboardingDocument::$documentTypeChoices,
            'disabled' => true,
        ]);
        $builder
            ->add('file', VichFileType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_uri' => static function ($object, $uri) use ($parentAdmin) {
                    /** @var OnboardingDocument|null $object */
                    if (null !== $object && null !== $parentAdmin) {
                        return $parentAdmin->generateObjectUrl('download', $object->getOnboarding(), ['documentId' => $object->getId()]);
                    }
                    return $uri;
                },
                'download_label' => true,
                'asset_helper' => true,
                'translation_domain' => 'messages',
            ]);
        $builder->addEventListener(FormEvents::POST_SET_DATA, static function (FormEvent $event) {
            /** @var OnboardingDocument $entity */
            $entity = $event->getData();
            $form = $event->getForm();
            if (null !== $entity && null !== $entity->getName()) {
                $form->add('name', TextType::class, [
                    'required' => true,
                    'disabled' => true,
                ]);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OnboardingDocument::class,
            'parent_admin' => null,
        ]);
    }


}