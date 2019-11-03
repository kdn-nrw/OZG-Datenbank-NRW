<?php

namespace App\Entity\Base;

/**
 * Base named entity (hideable)
 */
abstract class BaseNamedEntity extends BaseEntity implements NamedEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;
}