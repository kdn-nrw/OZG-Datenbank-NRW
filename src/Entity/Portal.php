<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Mindbase\EntityBundle\Entity\HideableEntityTrait;
use Mindbase\EntityBundle\Entity\NamedEntityInterface;
use Mindbase\EntityBundle\Entity\NamedEntityTrait;
use Mindbase\EntityBundle\Entity\SoftdeletableEntityInterface;
use Mindbase\EntityBundle\Entity\SoftdeletableEntityTrait;


/**
 * Class Portal
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_portal")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\HasLifecycleCallbacks
 */
class Portal extends BaseEntity implements SoftdeletableEntityInterface, NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;
    use SoftdeletableEntityTrait;

    /**
     * @var ServiceProvider
     * @ORM\ManyToOne(targetEntity="ServiceProvider", inversedBy="solutions", cascade={"persist"})
     */
    private $serviceProvider;

    /**
     * Url
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * Description
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var Solution[]|Collection
     * @ORM\ManyToMany(targetEntity="Solution", mappedBy="portals")
     */
    private $solutions;

    public function __construct()
    {
        $this->solutions = new ArrayCollection();
    }

    /**
     * @return ServiceProvider
     */
    public function getServiceProvider()
    {
        return $this->serviceProvider;
    }

    /**
     * @param ServiceProvider $serviceProvider
     */
    public function setServiceProvider($serviceProvider): void
    {
        $this->serviceProvider = $serviceProvider;
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
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

    /**
     * @param Solution $solution
     * @return self
     */
    public function addSolution($solution)
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions->add($solution);
            $solution->addPortal($this);
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
            $solution->removePortal($this);
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
