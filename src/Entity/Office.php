<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Mindbase\EntityBundle\Entity\HideableEntityTrait;
use Mindbase\EntityBundle\Entity\NamedEntityInterface;
use Mindbase\EntityBundle\Entity\NamedEntityTrait;


/**
 * Class Office
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_office")
 * @ORM\HasLifecycleCallbacks
 */
class Office extends BaseBlamableEntity implements NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;

    /**
     * ozgrgbeschreibung
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var Commune
     * @ORM\ManyToOne(targetEntity="Commune", inversedBy="offices", cascade={"persist"})
     */
    private $commune;

    /**
     * Url
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * Contact
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $contact;

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
     * @return Commune
     */
    public function getCommune()
    {
        return $this->commune;
    }

    /**
     * @param Commune $commune
     */
    public function setCommune($commune): void
    {
        $this->commune = $commune;
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
    public function getContact(): ?string
    {
        return $this->contact;
    }

    /**
     * @param string|null $contact
     */
    public function setContact(?string $contact): void
    {
        $this->contact = $contact;
    }

}
