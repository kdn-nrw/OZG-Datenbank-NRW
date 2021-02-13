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

use App\Entity\Base\BaseNamedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class LAGE
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_situation")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource
 */
class Situation extends BaseNamedEntity
{

    /**
     * @var Subject
     * @ORM\ManyToOne(targetEntity="Subject", inversedBy="situations", cascade={"persist"})
     */
    private $subject;

    /**
     * @var ServiceSystem[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\ServiceSystem", mappedBy="situation")
     */
    private $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

    /**
     * @return Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param Subject $subject
     */
    public function setSubject($subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return Service[]|Collection
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param Service[]|Collection $services
     */
    public function setServices($services): void
    {
        $this->services = $services;
    }

}
