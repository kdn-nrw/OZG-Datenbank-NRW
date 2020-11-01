<?php

namespace App\Api\Consumer\Validator\Constraints;

use App\Api\Consumer\Model\ZuFiDemand as ZuFiDemandModel;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ZuFiDemandValidator extends ConstraintValidator
{

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var ZuFiDemand $constraint */
        /** @var ZuFiDemandModel $demand */
        $demand = $value;
        $hasKey = !empty($demand->getServiceKey()) || !empty($demand->getCustomKey());
        $hasZipCode = !empty($demand->getZipCode());
        $hasRegionalKey = !empty($demand->getRegionalKey());
        $demandValid = $hasKey && ($hasZipCode || $hasRegionalKey);
        if (!$demandValid) {
            $this->context->buildViolation($constraint->messageInvalid)
                ->atPath('serviceKey')
                ->addViolation();
        }
    }
}