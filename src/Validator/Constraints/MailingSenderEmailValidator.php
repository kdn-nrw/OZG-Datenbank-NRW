<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\VarDumper\VarDumper;

class MailingSenderEmailValidator extends ConstraintValidator
{
    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        $domainValid = strpos($value, '@kdn.de') !== false;
        /** @var MailingSenderEmail $constraint */
        if (!$domainValid) {
            $this->context->buildViolation($constraint->messageInvalidDomain)
                ->atPath('senderEmail')
                ->setTranslationDomain('messages')
                ->addViolation();
        }
    }
}