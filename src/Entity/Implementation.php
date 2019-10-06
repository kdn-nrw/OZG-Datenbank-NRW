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
 * Class Umsetzung
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_implementation")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\HasLifecycleCallbacks
 */
class Implementation extends BaseEntity implements SoftdeletableEntityInterface, NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;
    use SoftdeletableEntityTrait;

    /**
     * umtzgfinal (ja/nein)
     * @var bool
     *
     * @ORM\Column(name="is_final", type="boolean")
     */
    protected $isFinal = false;

    /**
     * geplant (ja/nein)
     * @var bool
     *
     * @ORM\Column(name="is_planned", type="boolean")
     */
    protected $isPlanned = false;
/*
fim (ja/nein)
steckbr (ja/nein)
fimurl
 */
}
