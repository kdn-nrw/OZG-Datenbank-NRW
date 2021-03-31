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
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User assigned entity trait
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 * @since     2021-03-20
 */
trait UserAssignedEntityTrait
{

    /**
     * User ID who is assigned to the entity
     *
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(nullable=true, name="user_id", onDelete="SET NULL")
     *
     * @var UserInterface|null
     */
    protected $user;

    /**
     * Sets user.
     *
     * @param UserInterface|null $user
     *
     * @return $this
     */
    public function setUser(?UserInterface $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Returns user.
     *
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }
}
