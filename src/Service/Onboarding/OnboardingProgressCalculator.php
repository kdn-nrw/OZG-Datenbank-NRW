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

namespace App\Service\Onboarding;

use App\DependencyInjection\InjectionTraits\InjectManagerRegistryTrait;
use App\Entity\MetaData\AbstractMetaItem;
use App\Entity\MetaData\CalculateCompletenessEntityInterface;
use App\Entity\MetaData\MetaItemProperty;
use App\Entity\Onboarding\AbstractOnboardingEntity;
use App\Entity\Onboarding\CommuneInfo;
use App\Entity\Onboarding\Contact;
use App\Entity\Onboarding\Epayment;
use App\Entity\Onboarding\EpaymentProject;
use App\Entity\Onboarding\FormSolution;
use App\Entity\Onboarding\OnboardingDocument;
use App\Entity\Onboarding\Release;
use App\Entity\Onboarding\ServiceAccount;
use App\Entity\Onboarding\XtaServer;
use App\Entity\StateGroup\Commune;
use App\Service\MetaData\InjectMetaDataManagerTrait;
use Doctrine\Common\Collections\Collection;

class OnboardingProgressCalculator
{
    use InjectMetaDataManagerTrait;

    /**
     * Returns the information for the completion state
     *
     * @param CalculateCompletenessEntityInterface $object
     * @return array
     */
    public function getCompletionInfo(CalculateCompletenessEntityInterface $object): array
    {
        $calcProperties = $this->getRequiredPropertiesForCompletion($object);
        $ratePerProperty = ceil(100 / count($calcProperties));
        $info = [
            'required_properties' => $calcProperties,
            'invalid_properties' => [],
            'properties_state' => [],
            'completed_properties_count' => 0,
            'rate_per_property' => $ratePerProperty,
        ];
        $completionRate = 0;
        foreach ($calcProperties as $property) {
            if (is_array($property)) {
                $propertyIsFilled = false;
                foreach ($property as $orProperty) {
                    $propertyIsFilled = $this->isPropertyCompleted($object, $orProperty);
                    if ($propertyIsFilled) {
                        break;
                    }
                }
                $info['properties_state'][implode('_OR_', $property)] = $propertyIsFilled;
            } else {
                $propertyIsFilled = $this->isPropertyCompleted($object, $property);
                $info['properties_state'][$property] = $propertyIsFilled;
            }
            if ($propertyIsFilled) {
                ++$info['completed_properties_count'];
                $completionRate += $ratePerProperty;
            } else {
                $info['invalid_properties'][] = $property;
            }
        }
        $info['total'] = min(100, $completionRate);
        return $info;
    }

    /**
     * @param string $objectClass
     * @return array
     */
    protected function getRequiredPropertiesForCompletionFromMetaData(string $objectClass): array
    {
        $objectMetaData = $this->metaDataManager->getObjectClassMetaData($objectClass);
        $properties = [];
        if (null !== $objectMetaData) {
            $allowedMetaTypes = [AbstractMetaItem::META_TYPE_FIELD, AbstractMetaItem::META_TYPE_ADMIN_FIELD];
            $objectPropertiesMeta = $objectMetaData->getMetaItemProperties();
            foreach ($objectPropertiesMeta as $propertyMeta) {
                if ($propertyMeta->getUseForCompletenessCalculation()
                    && ($metaKey = $propertyMeta->getMetaKey())
                    && in_array($propertyMeta->getMetaType(), $allowedMetaTypes, false)) {
                    $properties[] = $this->convertToPropertyName($metaKey);
                }
            }
        }
        return $properties;
    }

    /**
     * Returns the field name converted to a property name (lower camel case)
     *
     * @param string $input
     * @return string
     */
    protected function convertToPropertyName(string $input): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_', ' '], ' ', $input))));
    }

    /**
     * Returns the properties of the given entity, that are required for the entity to be marked as completed
     *
     * @param CalculateCompletenessEntityInterface $object
     * @return array|string[]
     */
    protected function getRequiredPropertiesForCompletion(CalculateCompletenessEntityInterface $object): array
    {
        $objectClass = get_class($object);
        $properties = $this->getRequiredPropertiesForCompletionFromMetaData($objectClass);
        // Fallback in case no eta data were set
        switch (get_class($object)) {
            case CommuneInfo::class:
                if (empty($properties)) {
                    $properties = ['contacts', 'privacyText', 'imprintText', 'accessibility', 'ipAddress', 'openingHours', 'imageName', 'communeSolutions',];
                }
                break;
            case Epayment::class:
                if (empty($properties)) {
                    $properties = [
                        //'testIpAddress',
                        'street', 'zipCode', 'town',
                        //'paymentUser',
                        'mandatorEmail',
                        // addManagerFormFields
                        'cashRegisterPersonalAccountNumber', 'lengthReceiptNumber',
                        // 'cashRegisterCheckProcedureStatus',
                        'lengthFirstAccountAssignmentInformation', 'contentFirstAccountAssignmentInformation',
                        //'lengthSecondAccountAssignmentInformation', 'contentSecondAccountAssignmentInformation',
                        'managerNo', 'applicationName',
                        // configureFormFields
                        // 'paymentProviderAccountId',
                        //'projects',
                        'testIpAddress', 'epaymentServices',
                    ];
                } else {
                    $additionalProperties = $this->getRequiredPropertiesForCompletionFromMetaData(Commune::class);
                    $mapProperties = [
                        'street' => 'organisation.street', 
                        'zipCode' => 'organisation.zipCode',
                        'town' => 'organisation.town',
                    ];
                    foreach ($mapProperties as $property => $metaProperty) {
                        if (!in_array($property, $properties) && in_array($metaProperty, $additionalProperties)) {
                            $properties[] = $property;
                        }
                    }
                }
                break;
            case FormSolution::class:
                if (empty($properties)) {
                    $properties = [
                        ['privacyText', 'privacyUrl'], ['imprintText', 'imprintUrl'],
                        'accessibility', 'licenseConfirmed',
                        'administrationPhoneNumber', 'administrationFaxNumber', 'administrationEmail', 'administrationUrl',
                        'letterheadAddress', 'openingHours',
                        'contacts',
                    ];
                }
                break;
            case Release::class:
                if (empty($properties)) {
                    $properties = [
                        'releaseStatus', 'releaseDate',
                        //'releaseConfirmed',
                    ];
                }
                break;
            case XtaServer::class:
                if (empty($properties)) {
                    $properties = [
                        'applicationType', 'organizationalKey', 'intermediaryOperatorType',
                        'contact', 'osciPrivateKeyPassword',
                        'documents',
                    ];
                }
                break;
            case ServiceAccount::class:
                if (empty($properties)) {
                    $properties = [
                        // addMandatorFormFields
                        'street', 'zipCode', 'town',
                        //'paymentUser',
                        'mandatorEmail',
                        // addMandatorAccountFormFields
                        'answerUrl1', 'clientId', 'clientPassword',
                    ];
                } else {
                   // $additionalProperties = $this->getRequiredPropertiesForCompletionFromMetaData(Commune::class);
                    //$properties = array_merge($additionalProperties, $properties);
                }
                break;
            case EpaymentProject::class:
                if (empty($properties)) {
                    $properties = ['projectId', 'projectPassword'];
                }
                break;
            case OnboardingDocument::class:
                if (empty($properties)) {
                    $properties = ['name'];
                }
                break;
            case Contact::class:
                /** @var Contact $object */
                if (empty($properties)) {
                    $properties = ['firstName', 'email', 'lastName', 'phoneNumber'];
                    if ($object->getContactType() === Contact::CONTACT_TYPE_EPAYMENT_USER) {
                        $properties[] = 'externalUserName';
                        $properties[] = 'mobileNumber';
                    }
                } else {
                    if ($object->getContactType() !== Contact::CONTACT_TYPE_EPAYMENT_USER) {
                        $removeFields = ['externalUserName', 'mobileNumber'];
                        $properties = array_diff($properties, $removeFields);
                    }
                }
                break;
            default:
                $properties = [];
                break;
        }
        return $properties;
    }

    /**
     * Calculates the completion rate for this entity
     *
     * @return int
     */
    public function calculateCompletionRate(CalculateCompletenessEntityInterface $object): int
    {
        $info = $this->getCompletionInfo($object);
        $newCompletionRate = (int)round($info['total']);
        if ($object instanceof AbstractOnboardingEntity) {
            $object->setCompletionRate($newCompletionRate);
        }
        return $newCompletionRate;
    }

    /**
     * Returns true, if the given property is filled
     * @param string $property
     * @return bool
     */
    protected function isPropertyCompleted(CalculateCompletenessEntityInterface $object, string $property): bool
    {
        $getter = 'get' . ucfirst($property);
        if (!method_exists($object, $getter)) {
            $getter = 'is' . ucfirst($property);
        }
        $value = $object->$getter();
        if ($value instanceof Collection) {
            $itemCount = count($value);
            $hasIncompleteSubItems = false;
            foreach ($value as $item) {
                if ($item instanceof CalculateCompletenessEntityInterface) {
                    if ($this->calculateCompletionRate($item) < 100) {
                        $hasIncompleteSubItems = true;
                        break;
                    }
                } elseif (method_exists($item, 'calculateCompletionRate')) {
                    if ($item->calculateCompletionRate() < 100) {
                        $hasIncompleteSubItems = true;
                        break;
                    }
                }
            }
            $isCompleted = $itemCount > 0 && !$hasIncompleteSubItems;
        } elseif ($value instanceof CalculateCompletenessEntityInterface) {
            $isCompleted = $this->calculateCompletionRate($value) === 100;
        } else {
            $isCompleted = !empty($value);
        }
        return $isCompleted;
    }
}