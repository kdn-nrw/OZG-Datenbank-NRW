<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Mindbase\EntityBundle\Entity\HideableEntityTrait;
use Mindbase\EntityBundle\Entity\NamedEntityInterface;
use Mindbase\EntityBundle\Entity\NamedEntityTrait;


/**
 * Class Formularserver
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_form_server")
 * @ORM\HasLifecycleCallbacks
 */
class FormServer extends BaseEntity implements NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;
    use UrlTrait;

    /**
     * @var Solution[]|Collection
     * @ORM\ManyToMany(targetEntity="Solution", inversedBy="formServers")
     * @ORM\JoinTable(name="ozg_form_servers_solutions",
     *     joinColumns={
     *     @ORM\JoinColumn(name="form_server_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="solution_id", referencedColumnName="id")
     *   }
     * )
     */
    private $solutions;

    public function __construct()
    {
        $this->solutions = new ArrayCollection();
    }

    /**
     * @param Solution $solution
     * @return self
     */
    public function addSolution($solution)
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->addFormServer($this);
        }

        return $this;
    }

    /**
     * @param Solution $solution
     * @return self
     */
    public function removeSolution($solution)
    {
        if ($this->solutions->contains($solution)) {
            $this->solutions->removeElement($solution);
            $solution->removeFormServer($this);
        }

        return $this;
    }

    /**
     * @return Solution[]|Collection
     */
    public function getSolutions()
    {
        return $this->solutions;
    }

    /**
     * @param Solution[]|Collection $solutions
     */
    public function setSolutions($solutions): void
    {
        $this->solutions = $solutions;
    }
}
