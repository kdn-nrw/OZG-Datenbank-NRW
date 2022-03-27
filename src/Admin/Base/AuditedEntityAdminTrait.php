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

namespace App\Admin\Base;

use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;

/**
 * Class AuditedEntityAdminTrait
 *
 * @property LabelTranslatorStrategyInterface $labelTranslatorStrategy
 */
trait AuditedEntityAdminTrait
{

    /**
     * Flag for audited entities; Used to toggle history action
     * @var bool
     */
    protected $entityAuditEnabled = false;

    /**
     * @return bool
     */
    public function isEntityAuditEnabled(): bool
    {
        return $this->entityAuditEnabled;
    }

    /**
     * @param bool $entityAuditEnabled
     */
    public function setEntityAuditEnabled(bool $entityAuditEnabled): void
    {
        $this->entityAuditEnabled = $entityAuditEnabled;
    }
}
