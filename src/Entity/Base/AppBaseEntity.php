<?php

namespace App\Entity\Base;

/**
 * Base named entity (hideable)
 */
abstract class AppBaseEntity extends BaseNamedEntity implements BlameableInterface
{
    use BlameableTrait;
}