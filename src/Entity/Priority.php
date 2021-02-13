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

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Priority
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_priority")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource
 */
class Priority extends AbstractStatus
{
    /**
     * Previous status
     * @var Priority|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Priority")
     * @ORM\JoinColumn(name="prev_priority_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $prevStatus;

    /**
     * Next status
     * @var Priority|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Priority")
     * @ORM\JoinColumn(name="next_priority_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $nextStatus;

    /**
     * @var ServiceSystem[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\ServiceSystem", mappedBy="priority")
     */
    private $serviceSystems;

    public function __construct()
    {
        $this->serviceSystems = new ArrayCollection();
    }

    /**
     * @return ServiceSystem[]|Collection
     */
    public function getServiceSystems()
    {
        return $this->serviceSystems;
    }

    /**
     * @param ServiceSystem[]|Collection $serviceSystems
     */
    public function setServiceSystems($serviceSystems): void
    {
        $this->serviceSystems = $serviceSystems;
    }

    /**
     * @return Priority|null
     */
    public function getPrevStatus(): ?StatusEntityInterface
    {
        return $this->prevStatus;
    }

    /**
     * @param Priority|null $prevStatus
     */
    public function setPrevStatus(?Priority $prevStatus): void
    {
        $this->prevStatus = $prevStatus;
    }

    /**
     * @return Priority|null
     */
    public function getNextStatus(): ?StatusEntityInterface
    {
        return $this->nextStatus;
    }

    /**
     * @param Priority|null $nextStatus
     */
    public function setNextStatus(?Priority $nextStatus): void
    {
        $this->nextStatus = $nextStatus;
    }

}
