<?php

namespace App\Validator\Constraints;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MailingSenderEmail extends Constraint
{

    public $messageInvalidDomain = 'app.mailing.validation.invalid_sender_domain';

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        # This is the important bit.
        return self::CLASS_CONSTRAINT;
    }
}