<?php

namespace App\Entity;

use Mindbase\EntityBundle\Entity\BlameableInterface;
use Mindbase\EntityBundle\Entity\BlameableTrait;

/**
 * Class BaseEntity
 */
abstract class BaseBlamableEntity extends BaseEntity implements BlameableInterface
{
    use BlameableTrait;
}