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

namespace App\Validator\Constraints;

use App\Entity\Onboarding\EpaymentService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OnboardingEpaymentServicesValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (($value instanceof EpaymentService) && null !== $ePayment = $value->getEpayment()) {
            $isValid = true;
            /** @var OnboardingEpaymentServices $constraint */
            $field = $constraint->field;
            if ($field === 'valueSecondAccountAssignmentInformation') {
                $checkVal = trim((string)$value->getValueSecondAccountAssignmentInformation());
                $keyLength = (int)$ePayment->getLengthSecondAccountAssignmentInformation();
                $message = $constraint->messageInvalidKeyLengthSecond;
            } else {
                $checkVal = trim((string)$value->getValueFirstAccountAssignmentInformation());
                $keyLength = (int)$ePayment->getLengthFirstAccountAssignmentInformation();
                $message = $constraint->messageInvalidKeyLengthFirst;
            }
            if ($checkVal && $keyLength > 0 && mb_strlen($checkVal) !== $keyLength) {
                $isValid = false;
            }
            $solutionName = '';
            if ($solution = $value->getSolution()) {
                $solutionName = $solution . '';
            }
            /** @var OnboardingEpaymentServices $constraint */
            if (!$isValid) {
                $this->context->buildViolation($message)
                    ->atPath('epaymentServices')
                    ->setParameters([
                        'keyLength' => $keyLength,
                        'solutionName' => $solutionName
                    ])
                    ->setTranslationDomain('messages')
                    ->addViolation();
            }
        }
    }
}