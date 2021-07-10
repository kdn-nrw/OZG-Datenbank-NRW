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
 * interface ImplementationStatusInfoInterface
 * Process an entity reference property to find problems when deleting an entity
 *
 * @package App\Model
 */
interface ImplementationStatusInfoInterface
{

    /**
     * @return ImplementationStatus|null
     */
    public function getStatus(): ?ImplementationStatus;

    /**
     * @return DateTime|null
     */
    public function getProjectStartAt(): ?DateTime;

    /**
     * @return DateTime|null
     */
    public function getConceptStatusAt(): ?DateTime;

    /**
     * @return DateTime|null
     */
    public function getImplementationStatusAt(): ?DateTime;

    /**
     * @return DateTime|null
     */
    public function getPilotingStatusAt(): ?DateTime;

    /**
     * @return DateTime|null
     */
    public function getCommissioningStatusAt(): ?DateTime;

    /**
     * @return DateTime|null
     */
    public function getNationwideRolloutAt(): ?DateTime;

    /**
     * Returns the status date for the given status
     *
     * @param ImplementationStatus $status
     * @return DateTime|null
     */
    public function getStatusDate(ImplementationStatus $status): ?DateTime;

    /**
     * Returns the true if the given status is active
     *
     * @param ImplementationStatus $status
     * @return bool
     */
    public function isStatusActive(ImplementationStatus $status): bool;
}
