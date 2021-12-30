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

namespace App\Entity\ModelRegion;

use App\Entity\Base\BaseNamedEntity;
use App\Entity\UrlTrait;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class ModelRegionProjectWebsite
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_model_region_project_website")
 */
class ModelRegionProjectWebsite extends BaseNamedEntity
{
    use UrlTrait;

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var ModelRegionProject|null
     * @ORM\ManyToOne(targetEntity="App\Entity\ModelRegion\ModelRegionProject", inversedBy="websites")
     * @ORM\JoinColumn(name="model_region_project_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $modelRegionProject;

    /**
     * @return ModelRegionProject|null
     */
    public function getModelRegionProject(): ?ModelRegionProject
    {
        return $this->modelRegionProject;
    }

    /**
     * @param ModelRegionProject|null $modelRegionProject
     */
    public function setModelRegionProject(?ModelRegionProject $modelRegionProject): void
    {
        $this->modelRegionProject = $modelRegionProject;
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

    public function __toString(): string
    {
        return $this->getName() ?: 'n/a';
    }
}
