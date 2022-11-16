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

namespace App\Entity\Application;

use App\Entity\Base\BaseNamedEntity;
use App\Entity\SpecializedProcedure;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class ApplicationModule
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_specialized_procedure_module")
 */
class ApplicationModule extends BaseNamedEntity
{
    /**
     * @var SpecializedProcedure
     * @ORM\ManyToOne(targetEntity="App\Entity\SpecializedProcedure", inversedBy="applicationModules", cascade={"persist"})
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $application;

    /**
     * Description
     *
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description = '';

    /**
     * @return SpecializedProcedure|null
     */
    public function getApplication(): ?SpecializedProcedure
    {
        return $this->application;
    }

    /**
     * @param SpecializedProcedure $application
     */
    public function setApplication(SpecializedProcedure $application): void
    {
        $this->application = $application;
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
