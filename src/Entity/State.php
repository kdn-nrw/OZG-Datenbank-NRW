<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Mindbase\EntityBundle\Entity\HideableEntityTrait;
use Mindbase\EntityBundle\Entity\NamedEntityInterface;
use Mindbase\EntityBundle\Entity\NamedEntityTrait;


/**
 * Class Bundeslaender
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_state")
 * @ORM\HasLifecycleCallbacks
 */
class State extends BaseEntity implements NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;

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
