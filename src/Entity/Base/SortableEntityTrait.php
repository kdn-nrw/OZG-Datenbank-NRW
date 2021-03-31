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

namespace App\Entity\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sortable entity trait (provides field "position")
 */
trait SortableEntityTrait
{

    /**
     * @ORM\Column(type="integer", name="position", nullable=true)
     * @var int
     */
    private $position = 0;

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return (int)$this->position;
    }

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void
    {
        $this->position = (int)$position;
    }
}
