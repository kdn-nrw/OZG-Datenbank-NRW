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

use App\Entity\Base\BaseEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class form server solution
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_form_servers_solutions")
 */
class FormServerSolution extends BaseEntity
{

    /**
     * @var FormServer|null
     * @ORM\ManyToOne(targetEntity="FormServer", inversedBy="formServerSolutions", cascade={"persist"})
     * @ORM\JoinColumn(name="form_server_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $formServer;

    /**
     * @ORM\Column(type="integer", name="position", nullable=true)
     * @var int
     */
    private $position = 0;

    /**
     * @var Solution|null
     * @ORM\ManyToOne(targetEntity="Solution", inversedBy="formServerSolutions", cascade={"persist"})
     * @ORM\JoinColumn(name="solution_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $solution;

    /**
     * Status
     * @var Status|null
     *
     * @ORM\ManyToOne(targetEntity="Status")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $status;

    /**
     * Article number
     * @var string|null
     *
     * @ORM\Column(name="article_number", type="string", length=255, nullable=true)
     */
    private $articleNumber;

    /**
     * Assistant type
     * @var string|null
     *
     * @ORM\Column(name="assistant_type", type="string", length=255, nullable=true)
     */
    private $assistantType;

    /**
     * Article key/identifier
     * @var string|null
     *
     * @ORM\Column(name="article_key", type="string", length=255, nullable=true)
     */
    private $articleKey;

    /**
     * Usable as print template
     * @var bool
     *
     * @ORM\Column(name="usable_as_print_template", type="boolean", nullable=true)
     */
    protected $usableAsPrintTemplate = false;

    /**
     * @return FormServer|null
     */
    public function getFormServer(): ?FormServer
    {
        return $this->formServer;
    }

    /**
     * @param FormServer $formServer
     */
    public function setFormServer(FormServer $formServer): void
    {
        $this->formServer = $formServer;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return (int) $this->position;
    }

    /**
     * @param int|null $position
     */
    public function setPosition(?int $position): void
    {
        $this->position = (int) $position;
    }

    /**
     * @return Solution|null
     */
    public function getSolution(): ?Solution
    {
        return $this->solution;
    }

    /**
     * @param Solution $solution
     */
    public function setSolution(Solution $solution): void
    {
        $this->solution = $solution;
    }

    /**
     * @return Status|null
     */
    public function getStatus(): ?Status
    {
        return $this->status;
    }

    /**
     * @param Status|null $status
     */
    public function setStatus(?Status $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string|null
     */
    public function getArticleNumber(): ?string
    {
        return $this->articleNumber;
    }

    /**
     * @param string|null $articleNumber
     */
    public function setArticleNumber(?string $articleNumber): void
    {
        $this->articleNumber = $articleNumber;
    }

    /**
     * @return string|null
     */
    public function getAssistantType(): ?string
    {
        return $this->assistantType;
    }

    /**
     * @param string|null $assistantType
     */
    public function setAssistantType(?string $assistantType): void
    {
        $this->assistantType = $assistantType;
    }

    /**
     * @return string|null
     */
    public function getArticleKey(): ?string
    {
        return $this->articleKey;
    }

    /**
     * @param string|null $articleKey
     */
    public function setArticleKey(?string $articleKey): void
    {
        $this->articleKey = $articleKey;
    }

    /**
     * @return bool
     */
    public function isUsableAsPrintTemplate(): bool
    {
        return (bool) $this->usableAsPrintTemplate;
    }

    /**
     * @param bool|null $usableAsPrintTemplate
     */
    public function setUsableAsPrintTemplate(?bool $usableAsPrintTemplate): void
    {
        $this->usableAsPrintTemplate = (bool) $usableAsPrintTemplate;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function __toString(): string
    {
        $formServer = $this->getFormServer();
        if (null === $formServer) {
            $name = (string)$this->getId();
        } else {
            $name = $formServer->getName();
            if (empty($name)) {
                $name = (string) $formServer->getId();
            }
        }
        if ($articleNr = $this->getArticleNumber()) {
            $name .= ' (' . $articleNr . ')';
        }
        return $name;
    }

}
