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
 * Class Bundeslaender
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_state")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\HasLifecycleCallbacks
 */
class State extends BaseEntity implements SoftdeletableEntityInterface, NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;
    use SoftdeletableEntityTrait;

    /**
     * bundeslandkzl
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $key;

    /**
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->key;
    }

    /**
     * @param string|null $key
     */
    public function setKey(?string $key): void
    {
        $this->key = $key;
    }

}
