<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait ColorCodedEntityTrait used for assigned colors and css classes (for colors) to entities
 */
trait ColorCodedEntityTrait
{

    /**
     * The color for this status (used in statistics)
     *
     * @var string|null
     *
     * @ORM\Column(name="color", type="string", length=8, nullable=true)
     */
    protected $color;

    /**
     * The css class used for this status
     *
     * @var string|null
     *
     * @ORM\Column(name="css_class", type="string", length=50, nullable=true)
     */
    protected $cssClass;

    /**
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string|null $color
     */
    public function setColor(?string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return string|null
     */
    public function getCssClass(): ?string
    {
        return $this->cssClass;
    }

    /**
     * @param string|null $cssClass
     */
    public function setCssClass(?string $cssClass): void
    {
        $this->cssClass = $cssClass;
    }
}
