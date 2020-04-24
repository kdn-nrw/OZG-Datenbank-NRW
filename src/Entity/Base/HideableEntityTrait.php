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

namespace App\Entity\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hideable entity trait (provides field "hidden")
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */
trait HideableEntityTrait
{
    /**
     * @var bool
     *
     * @ORM\Column(name="hidden", type="boolean")
     */
    protected $hidden = false;

    /**
     * @return bool
     */
    public function getHidden(): bool
    {
        return !empty($this->hidden);
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->getHidden();
    }

    /**
     * @param bool $hidden
     */
    public function setHidden(bool $hidden)
    {
        $this->hidden = !empty($hidden);
    }
}
