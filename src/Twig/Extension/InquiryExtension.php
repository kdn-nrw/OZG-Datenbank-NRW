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

namespace App\Twig\Extension;

use App\Admin\ContextAwareAdminInterface;
use App\Admin\Onboarding\InquiryAdmin;
use App\Entity\Base\BaseEntityInterface;
use App\Entity\Onboarding\Inquiry;
use App\Model\ReferenceSettings;
use App\Service\InjectAdminManagerTrait;
use App\Service\InjectApplicationContextHandlerTrait;
use App\Translator\PrefixedUnderscoreLabelTranslatorStrategy;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class InquiryExtension extends AbstractExtension
{
    use InjectAdminManagerTrait;

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('inquiry_url', [$this, 'generateInquiryUrl']),
        ];
    }

    /**
     * Generates an inquiry url
     *
     * @param object|string $referenceSource
     * @param int|null $referenceId
     * @return string
     */
    public function generateInquiryUrl($referenceSource, ?int $referenceId): string
    {
        $sourceId = $referenceId;
        $sourceClass = is_object($referenceSource) ? get_class($referenceSource) : (string) $referenceSource;
        if (null === $referenceId && $referenceSource instanceof BaseEntityInterface) {
            $sourceId = $referenceSource->getId();
        }
        $inquiryAdmin = $this->adminManager->getAdminClassForEntityClass(Inquiry::class);
        if (null !== $inquiryAdmin) {
            /** @var InquiryAdmin $inquiryAdmin */
            return $inquiryAdmin->generateUrl('question', ['referenceSource' => $sourceClass, 'referenceId' => $sourceId]);
        }
        return '';
    }

    /**
     * Creates the label key for the given
     * @param string $entityClass
     * @param string $property
     * @return string
     */
    public function getClassPropertyLabel(string $entityClass, string $property = ''): string
    {
        return PrefixedUnderscoreLabelTranslatorStrategy::getClassPropertyLabel($entityClass, $property);
    }

    /**
     * @param object $object
     * @param FieldDescriptionInterface $fieldDescription
     * @return ReferenceSettings
     */
    public function getObjectFieldDescriptionMeta($object, FieldDescriptionInterface $fieldDescription): ReferenceSettings
    {
        $propertyConfiguration = $this->adminManager->getConfigurationForEntityProperty($object, $fieldDescription->getName());
        if ($propertyConfiguration['entity_class']) {
            return $this->getReferenceSettings($propertyConfiguration['entity_class'], $fieldDescription);
        }
        return $this->getDefaultReferenceSettings(get_class($object));
    }

    /**
     * @param string $entityClass The entity class name for which the settings are loaded
     * @param FieldDescriptionInterface|null $fieldDescription The optional field description (not set for custom fields)
     * @return ReferenceSettings
     */
    public function getReferenceSettings(
        string $entityClass,
        ?FieldDescriptionInterface $fieldDescription = null
    ): ReferenceSettings
    {
        $refAdmin = null;
        $isBackendMode = $this->applicationContextHandler->isBackend();
        $editRouteName = 'edit';
        if (null !== $fieldDescription && $fieldDescription->hasAssociationAdmin()
            && (null !== $tmpFieldAdmin = $fieldDescription->getAssociationAdmin())
            && $tmpFieldAdmin->getClass() === $entityClass) {
            $refAdmin = $tmpFieldAdmin;
            $editRouteName = $fieldDescription->getOption('route')['name'];
        } else {
            $adminClass = $this->adminManager->getAdminClassForEntityClass($entityClass);
            $refAdmin = $adminClass ? $this->adminManager->getAdminInstance($adminClass) : null;
        }
        if (null !== $refAdmin) {
            if ($refAdmin instanceof ContextAwareAdminInterface) {
                $settings = $refAdmin->getReferenceSettings($this->applicationContextHandler, $editRouteName);
            } else {
                $showRouteName = 'show';
                /** @var AbstractAdmin $refAdmin */
                $settings = new ReferenceSettings();
                $settings->setAdmin($refAdmin);
                $createShowLink = $refAdmin->hasRoute($showRouteName) && $refAdmin->hasAccess($showRouteName);
                $createEditLink = $isBackendMode && $refAdmin->hasRoute($editRouteName) && $refAdmin->hasAccess($editRouteName);
                $settings->setShow($createShowLink, $showRouteName);
                $settings->setEdit($createEditLink, $editRouteName);
                $settings->setLabelPrefix(PrefixedUnderscoreLabelTranslatorStrategy::getClassLabelPrefix($entityClass));
            }
        } else {
            $settings = $this->getDefaultReferenceSettings($entityClass);
        }
        $settings->setEntityClass($entityClass);
        return $settings;
    }

    /**
     * Creates default reference settings
     * @param string $entityClass
     * @return ReferenceSettings
     */
    private function getDefaultReferenceSettings(string $entityClass): ReferenceSettings
    {
        $settings = new ReferenceSettings();
        $settings->setShow(false);
        $settings->setEdit(false);
        $label = $this->getClassPropertyLabel($entityClass);
        $settings->setListTitle($label);
        $settings->setLabelPrefix(PrefixedUnderscoreLabelTranslatorStrategy::getClassLabelPrefix($entityClass));
        return $settings;
    }
}
