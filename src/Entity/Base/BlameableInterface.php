<?php
/**
 * Blamable trait
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */

namespace App\Entity\Base;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Blamable interface
 *
 * NOTICE: You have to add the following use statement to your class, which
 * is using this trait:
 * <pre>use Gedmo\Mapping\Annotation as Gedmo;</pre>
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
 */
interface BlameableInterface
{

    /**
     * Sets createdBy.
     *
     * @param UserInterface|null $createdBy
     *
     * @return $this
     */
    public function setCreatedBy(?UserInterface $createdBy = null);

    /**
     * Returns createdBy.
     *
     * @return UserInterface|null
     */
    public function getCreatedBy(): ?UserInterface;

    /**
     * Sets modifiedBy.
     *
     * @param UserInterface|null $modifiedBy
     *
     * @return $this
     */
    public function setModifiedBy(?UserInterface $modifiedBy = null);

    /**
     * Returns modifiedBy.
     *
     * @return UserInterface|null
     */
    public function getModifiedBy(): ?UserInterface;
}
