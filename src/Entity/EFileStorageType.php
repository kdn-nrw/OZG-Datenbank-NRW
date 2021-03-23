<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class EFileStorageType
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_efile_storage_type")
 * @ORM\HasLifecycleCallbacks
 */
class EFileStorageType extends BaseNamedEntity
{
    /**
     * Description
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var EFile[]|Collection
     * @ORM\ManyToMany(targetEntity="App\Entity\EFile", mappedBy="storageTypes")
     */
    private $eFiles;

    public function __construct()
    {
        $this->eFiles = new ArrayCollection();
    }

    /**
     * @param EFile $eFile
     * @return self
     */
    public function addEFile($eFile): self
    {
        if (!$this->eFiles->contains($eFile)) {
            $this->eFiles->add($eFile);
            $eFile->addStorageType($this);
        }

        return $this;
    }

    /**
     * @param EFile $eFile
     * @return self
     */
    public function removeEFile($eFile): self
    {
        if ($this->eFiles->contains($eFile)) {
            $this->eFiles->removeElement($eFile);
            $eFile->removeStorageType($this);
        }

        return $this;
    }

    /**
     * @return EFile[]|Collection
     */
    public function getEFiles()
    {
        return $this->eFiles;
    }

    /**
     * @param EFile[]|Collection $eFiles
     */
    public function setEFiles($eFiles): void
    {
        $this->eFiles = $eFiles;
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
}
