<?php

namespace App\Api\Consumer\Validator\Constraints;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ZuFiDemand extends Constraint
{

    public $messageInvalid = 'app.api.zu_fi.validator.invalid_parameters';

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        # This is the important bit.
        return self::CLASS_CONSTRAINT;
    }
}