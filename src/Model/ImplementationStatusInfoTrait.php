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

namespace App\Model;

use App\Entity\ImplementationStatus;
use DateTime;

/**
 * Trait ImplementationStatusInfoTrait
 *
 * @package App\Model
 * @see \App\Model\ImplementationStatusInfoInterface
 * @method getStatus(): ?ImplementationStatus
 * @method getProjectStartAt(): ?DateTime
 * @method getConceptStatusAt(): ?DateTime
 * @method getImplementationStatusAt(): ?DateTime
 * @method getCommissioningStatusAt(): ?DateTime
 * @method getNationwideRolloutAt(): ?DateTime
 */
trait ImplementationStatusInfoTrait
{

    /**
     * Returns the status date for the given status
     *
     * @param ImplementationStatus $status
     * @return DateTime|null
     */
    public function getStatusDate(ImplementationStatus $status): ?DateTime
    {
        $statusDate = null;
        if (null !== $status && null !== $statusId = $status->getId()) {
            switch ($statusId) {
                case ImplementationStatus::STATUS_ID_PREPARED:
                    $statusDate = $this->getProjectStartAt();
                    break;
                case ImplementationStatus::STATUS_ID_CONCEPT:
                    $statusDate = $this->getConceptStatusAt();
                    break;
                case ImplementationStatus::STATUS_ID_IMPLEMENTATION:
                    $statusDate = $this->getImplementationStatusAt();
                    break;
                case ImplementationStatus::STATUS_ID_PILOTING:
                    $statusDate = $this->getPilotingStatusAt();
                    break;
                case ImplementationStatus::STATUS_ID_COMMISSIONING:
                    $statusDate = $this->getCommissioningStatusAt();
                    break;
                case ImplementationStatus::STATUS_ID_NATIONWIDE_ROLLOUT:
                    $statusDate = $this->getNationwideRolloutAt();
                    break;
            }
        }
        return $statusDate;
    }

    /**
     * Returns the true if the given status is active
     *
     * @param ImplementationStatus $status
     * @return bool
     */
    public function isStatusActive(ImplementationStatus $status): bool
    {
        $isActive = $status === $this->getStatus();
        if (!$isActive && null !== $statusId = $status->getId()) {
            $statusDate = $this->getStatusDate($status);
            $isActive = null !== $statusDate && $statusDate->getTimestamp() <= time();
        }
        return $isActive;
    }
}
