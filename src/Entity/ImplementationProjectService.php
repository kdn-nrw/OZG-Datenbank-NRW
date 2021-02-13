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

namespace App\Entity;

use App\Entity\Base\BaseEntity;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Base\HideableEntityTrait;


/**
 * Class implementation project service
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_implementation_project_service")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource
 */
class ImplementationProjectService extends BaseEntity
{
    use HideableEntityTrait;

    /**
     * @var Service|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Service", inversedBy="implementationProjects", cascade={"persist"})
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     */
    private $service;

    /**
     * @var ImplementationProject|null
     * @ORM\ManyToOne(targetEntity="App\Entity\ImplementationProject", inversedBy="services", cascade={"persist"})
     * @ORM\JoinColumn(name="implementation_project_id", referencedColumnName="id")
     */
    private $implementationProject;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * Status
     * @var ImplementationStatus|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ImplementationStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $status;

    /**
     * @return Service|null
     */
    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * @param Service $service
     */
    public function setService(Service $service): void
    {
        $this->service = $service;
    }

    /**
     * @return ImplementationProject|null
     */
    public function getImplementationProject(): ?ImplementationProject
    {
        return $this->implementationProject;
    }

    /**
     * @param ImplementationProject $implementationProject
     */
    public function setImplementationProject(ImplementationProject $implementationProject): void
    {
        $this->implementationProject = $implementationProject;
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
     * @return ImplementationStatus|null
     */
    public function getStatus(): ?ImplementationStatus
    {
        return $this->status;
    }

    /**
     * @param ImplementationStatus|null $status
     */
    public function setStatus(?ImplementationStatus $status): void
    {
        $this->status = $status;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        $name = '';
        $project = $this->getImplementationProject();
        $service = $this->getService();
        if (null !== $project) {
            $name = $project->getName();
        }
        if (null !== $service) {
            $serviceSystem = $service->getServiceSystem();
            if (!empty($name)) {
                $name .= ': ';
            }
            $servicePrefix = '';
            if (null !== $serviceSystem) {
                $servicePrefix = 'OZG-Leistung ' . $serviceSystem->getName() .': Leika-Leistung ';
            }
            $name .= $servicePrefix . $service->getName();
        }
        if (empty($name)) {
            $name = (string) $this->getId();
        }
        return $name;
    }

}
