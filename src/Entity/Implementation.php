<?php

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Umsetzung
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_implementation")
 * @ORM\HasLifecycleCallbacks
 */
class Implementation extends BaseNamedEntity
{

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
