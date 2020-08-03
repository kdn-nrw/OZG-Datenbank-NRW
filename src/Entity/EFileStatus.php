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

use App\Entity\Base\BaseNamedEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class eFile status
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_efile_status")
 * @ORM\HasLifecycleCallbacks
 */
class EFileStatus extends BaseNamedEntity
{

    /**
     * statuslv
     * @var int
     *
     * @ORM\Column(name="level", type="integer")
     */
    private $level = 0;

    /**
     * statuslverkl
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }


}
