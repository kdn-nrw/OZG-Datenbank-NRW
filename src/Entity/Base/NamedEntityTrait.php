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
 * Named entity trait (provides field "name" and toString function)
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */
trait NamedEntityTrait
{

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * Set name
     *
     * @param string|null $name
     * @return self
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        if (null === $this->name) {
            return 'NULL';
        }
        return (string) $this->getName();
    }
}
