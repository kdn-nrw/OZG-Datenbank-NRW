<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class form server
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_form_server")
 * @ORM\HasLifecycleCallbacks
 */
class FormServer extends BaseNamedEntity
{
    use UrlTrait;

    /**
     * @var FormServerSolution[]|Collection
     * @ORM\OneToMany(targetEntity="FormServerSolution", mappedBy="formServer", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC", "name" = "ASC"})
     */
    private $formServerSolutions;

    public function __construct()
    {
        $this->formServerSolutions = new ArrayCollection();
    }

    /**
     * @param FormServerSolution $formServerSolution
     * @return self
     */
    public function addFormServerSolution(FormServerSolution $formServerSolution): self
    {
        if (!$this->formServerSolutions->contains($formServerSolution)) {
            $this->formServerSolutions->add($formServerSolution);
            $formServerSolution->setFormServer($this);
        }

        return $this;
    }

    /**
     * @param FormServerSolution $formServerSolution
     * @return self
     */
    public function removeFormServerSolution($formServerSolution): self
    {
        if ($this->formServerSolutions->contains($formServerSolution)) {
            $this->formServerSolutions->removeElement($formServerSolution);
        }

        return $this;
    }

    /**
     * @return FormServerSolution[]|Collection
     */
    public function getFormServerSolutions()
    {
        return $this->formServerSolutions;
    }

    /**
     * @param FormServerSolution[]|Collection $formServerSolutions
     */
    public function setFormServerSolutions($formServerSolutions): void
    {
        $this->formServerSolutions = $formServerSolutions;
    }
}
