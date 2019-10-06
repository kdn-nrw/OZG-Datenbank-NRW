<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ReflectionClass;
use Mindbase\EntityBundle\Entity\MindbaseEntityInterface;
use Mindbase\EntityBundle\Entity\TimestampableEntityInterface;
use Mindbase\EntityBundle\Entity\TimestampableEntityTrait;

/**
 * Class BaseEntity
 */
abstract class BaseEntity implements MindbaseEntityInterface, TimestampableEntityInterface
{
    use TimestampableEntityTrait;

    /**
     * @var null|int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return self
     */
    public function setId(?int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $reflectionClass = new ReflectionClass($this);
        $classShortName = $reflectionClass->getShortName();

        return sprintf('%s(%d)', $classShortName, $this->getId());
    }
}