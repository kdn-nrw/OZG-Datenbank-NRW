<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2021 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\Configuration;

use App\Entity\Base\BaseEntity;
use App\Entity\Base\BlameableInterface;
use App\Entity\Base\BlameableTrait;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class custom field
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_configuration_custom_value")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="record_type", type="string")
 */
class CustomValue extends BaseEntity implements BlameableInterface
{
    use BlameableTrait;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Configuration\CustomField", inversedBy="customValues")
     * @ORM\JoinColumn(name="custom_field_id", referencedColumnName="id", nullable=true)
     * @var \App\Entity\Configuration\CustomField|null
     */
    protected $customField;

    /**
     * The custom value
     *
     * @var string|null
     *
     * @ORM\Column(name="value", type="text", nullable=true)
     */
    private $value;

    /**
     * @return CustomField|null
     */
    public function getCustomField(): ?CustomField
    {
        return $this->customField;
    }

    /**
     * @param CustomField|null $customField
     */
    public function setCustomField(?CustomField $customField): void
    {
        $this->customField = $customField;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getValue();
    }


}
