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
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class OnboardingEpaymentServices extends Constraint
{
    /**
     * The field to be validated
     * @var string
     */
    public $field;

    public $messageInvalidKeyLengthFirst = 'app.epayment.validation.invalid_key_length_first';
    public $messageInvalidKeyLengthSecond = 'app.epayment.validation.invalid_key_length_second';

    /**
     * @inheritdoc
     */
    public function getTargets()
    {
        # This is the important bit.
        return self::CLASS_CONSTRAINT;
    }
}