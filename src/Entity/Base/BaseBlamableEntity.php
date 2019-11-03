<?php

namespace App\Entity\Base;

/**
 * Class BaseEntity
 */
abstract class BaseBlamableEntity extends BaseEntity implements BlameableInterface
{
    use BlameableTrait;
}