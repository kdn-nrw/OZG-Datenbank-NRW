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
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Subject (Themenfeld)
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_subject")
 */
class Subject extends BaseNamedEntity
{
    use ContactEntityTrait;

    /**
     * @var Situation[]|Collection
     * @ORM\OneToMany(targetEntity="Situation", mappedBy="subject", cascade={"all"})
     */
    private $situations;

    public function __construct()
    {
        $this->situations = new ArrayCollection();
    }

    /**
     * @param Situation $situation
     * @return self
     */
    public function addSituation(Situation $situation): self
    {
        if (!$this->situations->contains($situation)) {
            $this->situations->add($situation);
            $situation->setSubject($this);
        }

        return $this;
    }

    /**
     * @param Situation $situation
     * @return self
     */
    public function removeSituation(Situation $situation): self
    {
        if ($this->situations->contains($situation)) {
            $this->situations->removeElement($situation);
        }

        return $this;
    }

    /**
     * @return Situation[]|Collection
     */
    public function getSituations()
    {
        return $this->situations;
    }

    /**
     * @param Situation[]|Collection $situations
     */
    public function setSituations($situations): void
    {
        $this->situations = $situations;
    }

}
