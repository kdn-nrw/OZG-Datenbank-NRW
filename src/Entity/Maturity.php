<?php

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Reifegrad
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_maturity")
 * @ORM\HasLifecycleCallbacks
 */
class Maturity extends BaseNamedEntity
{

    const DEFAULT_ID = 7;

    /**
     * ozgrgbeschreibung
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

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
