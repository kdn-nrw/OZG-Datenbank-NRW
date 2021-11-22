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

use App\Entity\MetaData\CalculateCompletenessEntityInterface;
use App\Entity\Onboarding\AbstractOnboardingEntity;
use App\Entity\Onboarding\CommuneInfo;
use App\Entity\Onboarding\Contact;
use App\Entity\Onboarding\Epayment;
use App\Entity\Onboarding\EpaymentProject;
use App\Entity\Onboarding\FormSolution;
use App\Entity\Onboarding\Release;
use App\Entity\Onboarding\ServiceAccount;
use Doctrine\Common\Collections\Collection;

class OnboardingProgressCalculator
{
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

    protected function getRequiredPropertiesForCompletion(CalculateCompletenessEntityInterface $object): array
    {
        switch (get_class($object)) {
            case CommuneInfo::class:
                $properties = ['contacts', 'privacyText', 'imprintText', 'accessibility', 'openingHours', 'imageName'];
                break;
            case Epayment::class:
                $properties = [
                    'paymentProviderAccountId', 'paymentUser', 'mandatorEmail',
                    //'testIpAddress',
                    'street', 'zipCode', 'town',
                    'projects',
                    //'clientNumberIntegration', 'clientNumberProduction', 'managerNumber',
                    'budgetOffice', 'objectNumber',
                    'cashRegisterPersonalAccountNumber', 'indicatorDunningProcedure', 'bookingText', 'descriptionOfTheBookingList',
                    'managerNo', 'applicationName', 'lengthReceiptNumber', 'cashRegisterCheckProcedureStatus',
                    'lengthFirstAccountAssignmentInformation', 'lengthSecondAccountAssignmentInformation',
                    'contentFirstAccountAssignmentInformation', 'contentSecondAccountAssignmentInformation',
                ];
                break;
            case FormSolution::class:
                $properties = [
                    'contacts', ['privacyText', 'privacyUrl'], ['imprintText', 'imprintUrl'],
                    'accessibility', 'openingHours', 'imageName', 'letterheadAddress',
                    ];
                break;
            case Release::class:
                $properties = [
                    'releaseStatus', 'releaseDate', 'releaseConfirmed',
                ];
                break;
            case ServiceAccount::class:
                $properties = [
                    'paymentProviderAccountId', 'paymentUser', 'mandatorEmail',
                    'street', 'zipCode', 'town',
                ];
                break;
            case EpaymentProject::class:
                $properties = ['projectId', 'projectPassword'];
                break;
            case Contact::class:
                /** @var Contact $object */
                $properties = ['firstName', 'email', 'lastName', 'phoneNumber'];
                if ($object->getContactType() === Contact::CONTACT_TYPE_EPAYMENT_USER) {
                    $properties[] = 'externalUserName';
                    $properties[] = 'mobileNumber';
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