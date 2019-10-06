<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Mindbase\EntityBundle\Entity\HideableEntityTrait;
use Mindbase\EntityBundle\Entity\NamedEntityInterface;
use Mindbase\EntityBundle\Entity\NamedEntityTrait;


/**
 * Class Ministerium Land
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_ministry_state")
 * @ORM\HasLifecycleCallbacks
 */
class MinistryState extends BaseEntity implements NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;
}
