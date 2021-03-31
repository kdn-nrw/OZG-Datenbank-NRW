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

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Status
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_status")
 * @ORM\HasLifecycleCallbacks
 */
class Status extends AbstractStatus
{

    /**
     * Previous status
     * @var Status|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Status")
     * @ORM\JoinColumn(name="prev_status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $prevStatus;

    /**
     * Next status
     * @var Status|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Status")
     * @ORM\JoinColumn(name="next_status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $nextStatus;

    /**
     * @return Status|null
     */
    public function getPrevStatus(): ?StatusEntityInterface
    {
        return $this->prevStatus;
    }

    /**
     * @param Status|null $prevStatus
     */
    public function setPrevStatus(?Status $prevStatus): void
    {
        $this->prevStatus = $prevStatus;
    }

    /**
     * @return Status|null
     */
    public function getNextStatus(): ?StatusEntityInterface
    {
        return $this->nextStatus;
    }

    /**
     * @param Status|null $nextStatus
     */
    public function setNextStatus(?Status $nextStatus): void
    {
        $this->nextStatus = $nextStatus;
    }

}
