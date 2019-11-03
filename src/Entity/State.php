<?php

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Bundeslaender
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_state")
 * @ORM\HasLifecycleCallbacks
 */
class State extends BaseNamedEntity
{

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
