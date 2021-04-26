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

namespace App\EventSubscriber;

use App\Entity\Base\BaseEntityInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Contracts\EventDispatcher\Event as ContractEvent;

/**
 * Class EntityPostUpdateEvent
 */
class EntityPostUpdateEvent extends ContractEvent
{
    /**
     * @var BaseEntityInterface
     */
    protected $object;

    /**
     * @var AbstractAdmin
     */
    protected $admin;

    /**
     * EntityPostUpdateEvent constructor.
     * @param AdminInterface $admin
     * @param BaseEntityInterface $object
     */
    public function __construct(AdminInterface $admin, BaseEntityInterface $object)
    {
        $this->admin = $admin;
        $this->object = $object;
    }

    /**
     * Accessor to the object being manipulated.
     *
     * @return BaseEntityInterface
     */
    public function getObject(): BaseEntityInterface
    {
        return $this->object;
    }

    /**
     * @return AdminInterface
     */
    public function getAdmin(): AdminInterface
    {
        return $this->admin;
    }

}
