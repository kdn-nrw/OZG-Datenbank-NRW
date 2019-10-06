<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Mindbase\EntityBundle\Entity\HideableEntityTrait;
use Mindbase\EntityBundle\Entity\NamedEntityInterface;
use Mindbase\EntityBundle\Entity\NamedEntityTrait;
use Mindbase\EntityBundle\Entity\SoftdeletableEntityInterface;
use Mindbase\EntityBundle\Entity\SoftdeletableEntityTrait;


/**
 * Class InternalProcedure (Fachverfahren)
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_internal_procedure")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\HasLifecycleCallbacks
 */
class InternalProcedure extends BaseEntity implements SoftdeletableEntityInterface, NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;
    use SoftdeletableEntityTrait;

    /**
     * ozgrgbeschreibung
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $provider;

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
     * @return string|null
     */
    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * @param string|null $provider
     */
    public function setProvider(?string $provider): void
    {
        $this->provider = $provider;
    }

}
