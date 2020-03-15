<?php

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use App\Entity\Base\BlameableInterface;
use App\Entity\Base\BlameableTrait;
use App\Entity\Base\HideableEntityTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Abstract OZG service
 */
abstract class AbstractService extends BaseEntity implements BlameableInterface
{
    use BlameableTrait;
    use HideableEntityTrait;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    protected $name;

    /**
     * Service key
     * @var string|null
     *
     * @ORM\Column(type="string", name="service_key", length=255, nullable=true)
     */
    protected $serviceKey;

    /**
     * Description
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description = '';

    /**
     * Set name
     *
     * @param string $name
     * @return self
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getServiceKey(): ?string
    {
        return $this->serviceKey;
    }

    /**
     * @param string|null $serviceKey
     */
    public function setServiceKey(?string $serviceKey): void
    {
        $this->serviceKey = $serviceKey;
    }

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


    /**
     * @return Jurisdiction[]|Collection
     */
    abstract public function getJurisdictions();

    /**
     * @return Jurisdiction[]|Collection
     */
    abstract public function getRuleAuthorities();


    /**
     * Returns true if the state ministries property is active
     *
     * @return bool
     */
    public function getStateMinistriesEnabled(): bool
    {
        $jurisdictions = $this->getJurisdictions();
        foreach ($jurisdictions as $jurisdiction) {
            if ($jurisdiction->getId() === Jurisdiction::TYPE_STATE) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if the state ministries property is active
     *
     * @return bool
     */
    public function getBureausEnabled(): bool
    {
        $jurisdictions = $this->getJurisdictions();
        foreach ($jurisdictions as $jurisdiction) {
            if (in_array($jurisdiction->getId(), [Jurisdiction::TYPE_STATE, Jurisdiction::TYPE_COMMUNE], false)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if the rule authority state ministries property is active
     *
     * @return bool
     */
    public function getAuthorityStateMinistriesEnabled(): bool
    {
        $ruleAuthorities = $this->getRuleAuthorities();
        foreach ($ruleAuthorities as $jurisdiction) {
            if ($jurisdiction->getId() === Jurisdiction::TYPE_STATE) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns true if the rule authority bureau property is active
     *
     * @return bool
     */
    public function getAuthorityBureausEnabled(): bool
    {
        $ruleAuthorities = $this->getRuleAuthorities();
        foreach ($ruleAuthorities as $jurisdiction) {
            if ($jurisdiction->getId() === Jurisdiction::TYPE_COMMUNE) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return Bureau[]|Collection
     */
    abstract public function getBureaus();

    /**
     * @return MinistryState[]|Collection
     */
    abstract public function getStateMinistries();

    /**
     * @return Bureau[]|Collection
     */
    abstract public function getAuthorityBureaus();

    /**
     * @return MinistryState[]|Collection
     */
    abstract public function getAuthorityStateMinistries();

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        $name = (string)$this->getName();
        if (null === $name) {
            return '';
        }
        return $name;
    }
}
