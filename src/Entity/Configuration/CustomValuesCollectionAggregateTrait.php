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

namespace App\Entity\Configuration;

use Doctrine\Common\Collections\Collection;


/**
 * Trait CustomValuesCollectionAggregateTrait
 *
 * @author    Gert Hammes <gert.hammes@brain-appeal.com>
 * @copyright 2021 Brain Appeal GmbH (www.brain-appeal.com)
 * @license
 * @link      http://www.brain-appeal.com/
 * @since     2021-03-10
 * @property Collection|CustomValue[] $customValues
 */
trait CustomValuesCollectionAggregateTrait
{
    /**
     * @return Collection|CustomValue[]
     */
    public function getCustomValues(): Collection
    {
        return $this->customValues;
    }

    /**
     * @param Collection|CustomValue[] $customValues
     * @return self
     */
    public function setCustomValues($customValues)
    {
        $this->customValues = $customValues;

        return $this;
    }

    /**
     * @param CustomValue $customValue
     * @return void
     */
    public function addCustomValue(CustomValue $customValue): void
    {
        $customValues = $this->getCustomValues();
        if (!$customValues->contains($customValue)) {
            $customValues->add($customValue);
        }
    }

    /**
     * @param CustomValue $customValue
     * @return self
     */
    public function removeCustomValue(CustomValue $customValue): self
    {
        $customValues = $this->getCustomValues();
        if ($customValues->contains($customValue)) {
            $customValues->removeElement($customValue);
        }

        return $this;
    }
}
