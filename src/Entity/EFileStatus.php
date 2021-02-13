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

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class eFile status
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_efile_status")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource
 */
class EFileStatus extends AbstractStatus
{
    /**
     * Previous status
     * @var EFileStatus|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\EFileStatus")
     * @ORM\JoinColumn(name="prev_status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $prevStatus;

    /**
     * Next status
     * @var EFileStatus|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\EFileStatus")
     * @ORM\JoinColumn(name="next_status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $nextStatus;

    /**
     * @return EFileStatus|null
     */
    public function getPrevStatus(): ?StatusEntityInterface
    {
        return $this->prevStatus;
    }

    /**
     * @param EFileStatus|null $prevStatus
     */
    public function setPrevStatus(?EFileStatus $prevStatus): void
    {
        $this->prevStatus = $prevStatus;
    }

    /**
     * @return EFileStatus|null
     */
    public function getNextStatus(): ?StatusEntityInterface
    {
        return $this->nextStatus;
    }

    /**
     * @param EFileStatus|null $nextStatus
     */
    public function setNextStatus(?EFileStatus $nextStatus): void
    {
        $this->nextStatus = $nextStatus;
    }

}
