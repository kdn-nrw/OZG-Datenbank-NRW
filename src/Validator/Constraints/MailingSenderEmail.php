<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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