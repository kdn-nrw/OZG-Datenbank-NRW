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

use App\Entity\Base\BaseNamedEntity;
use App\Entity\Base\SortableEntityInterface;
use App\Entity\Base\SortableEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class custom field
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_configuration_custom_field")
 */
class CustomField extends BaseNamedEntity implements SortableEntityInterface
{
    use CustomValuesCollectionAggregateTrait;
    use SortableEntityTrait;

    /**
     * @var string|null
     *
     * @ORM\Column(name="field_label", type="string", length=255, nullable=true)
     */
    protected $fieldLabel;

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="record_type", type="string", length=255, nullable=true)
     */
    protected $recordType;

    /**
     * The field type
     *
     * @var string|null
     *
     * @ORM\Column(name="field_type", type="string", length=255, nullable=true)
     */
    protected $fieldType;

    /**
     * Field options
     *
     * @var string|null
     *
     * @ORM\Column(name="field_options", type="text", nullable=true)
     */
    private $fieldOptions = '';

    /**
     * Is required
     *
     * @var bool
     *
     * @ORM\Column(name="required", type="boolean")
     */
    protected $required = false;

    /**
     * Custom values for this field
     *
     * @var ArrayCollection|CustomValue[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Configuration\CustomValue", mappedBy="customField")
     */
    protected $customValues;

    public function __construct()
    {
        $this->customValues = new ArrayCollection();
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
     * @return string|null
     */
    public function getRecordType(): ?string
    {
        return $this->recordType;
    }

    /**
     * @param string|null $recordType
     */
    public function setRecordType(?string $recordType): void
    {
        $this->recordType = $recordType;
    }

    /**
     * @return string|null
     */
    public function getFieldType(): ?string
    {
        return $this->fieldType;
    }

    /**
     * @param string|null $fieldType
     */
    public function setFieldType(?string $fieldType): void
    {
        $this->fieldType = $fieldType;
    }

    /**
     * @return string|null
     */
    public function getFieldOptions(): ?string
    {
        return $this->fieldOptions;
    }

    /**
     * @param string|null $fieldOptions
     */
    public function setFieldOptions(?string $fieldOptions): void
    {
        $this->fieldOptions = $fieldOptions;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    /**
     * @return string|null
     */
    public function getFieldLabel(): ?string
    {
        return $this->fieldLabel;
    }

    /**
     * @param string|null $fieldLabel
     */
    public function setFieldLabel(?string $fieldLabel): void
    {
        $this->fieldLabel = $fieldLabel;
    }

}
