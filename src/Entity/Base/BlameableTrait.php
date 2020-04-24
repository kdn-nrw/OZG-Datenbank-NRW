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
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Blamable trait
 *
 * NOTICE: You have to add the following use statement to your class, which
 * is using this trait:
 * <pre>use Gedmo\Mapping\Annotation as Gedmo;</pre>
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */
trait BlameableTrait
{

    /**
     * User ID who created the entity
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(nullable=true, name="created_by", onDelete="SET NULL")
     *
     * @var UserInterface|null
     */
    protected $createdBy = null;

    /**
     * User ID who last modified the entity
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(nullable=true, name="modified_by", onDelete="SET NULL")
     *
     * @var UserInterface|null
     */
    protected $modifiedBy = null;

    /**
     * Sets createdBy.
     *
     * @param UserInterface|null $createdBy
     *
     * @return $this
     */
    public function setCreatedBy(?UserInterface $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Returns createdBy.
     *
     * @return UserInterface|null
     */
    public function getCreatedBy(): ?UserInterface
    {
        return $this->createdBy;
    }

    /**
     * Sets modifiedBy.
     *
     * @param UserInterface|null $modifiedBy
     *
     * @return $this
     */
    public function setModifiedBy(?UserInterface $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Returns modifiedBy.
     *
     * @return UserInterface|null
     */
    public function getModifiedBy(): ?UserInterface
    {
        return $this->modifiedBy;
    }
}
