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

use App\Entity\Base\BaseEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class search index word
 *
 * @ORM\Entity(repositoryClass="App\Entity\Repository\SearchIndexRepository")
 * @ORM\Table(name="ozg_search_index_word",indexes={
 *     @ORM\Index(name="search_idx", columns={"module", "record_id", "context"}),
 *     @ORM\Index(name="search_baseword", columns={"baseword"}),
 *     @ORM\Index(name="search_stopword", columns={"is_stopword"}),
 *     @ORM\Index(name="search_generated", columns={"is_generated"}),
 *     @ORM\Index(name="search_phonetic", columns={"metaphone"})
 * })
 */
class SearchIndexWord extends BaseEntity
{

    /**
     * Referenced module
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $module;

    /**
     * Referenced record id
     *
     * @var int
     *
     * @ORM\Column(name="record_id", type="integer", nullable=true)
     */
    private $recordId;

    /**
     * Result context (FE/BE)
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    protected $context;

    /**
     * Base word
     * @var string|null
     *
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $baseword;

    /**
     * Metaphone value
     *
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $metaphone = 0;

    /**
     * Is stop word
     *
     * @var bool
     *
     * @ORM\Column(name="is_stopword", type="boolean")
     */
    protected $isStopword = false;

    /**
     * Is generated word or word exists in content
     *
     * @var bool
     *
     * @ORM\Column(name="is_generated", type="boolean")
     */
    protected $isGenerated = false;

    /**
     * Is hidden (Temporary flag used when updating)
     *
     * @var bool
     *
     * @ORM\Column(name="hidden", type="boolean")
     */
    protected $hidden = false;

    /**
     * Word occurrence count
     *
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $occurrence = 0;

    /**
     * @return string|null
     */
    public function getModule(): ?string
    {
        return $this->module;
    }

    /**
     * @param string|null $module
     */
    public function setModule(?string $module): void
    {
        $this->module = $module;
    }

    /**
     * @return int
     */
    public function getRecordId(): int
    {
        return $this->recordId;
    }

    /**
     * @param int $recordId
     */
    public function setRecordId(int $recordId): void
    {
        $this->recordId = $recordId;
    }

    /**
     * @return string|null
     */
    public function getContext(): ?string
    {
        return $this->context;
    }

    /**
     * @param string|null $context
     */
    public function setContext(?string $context): void
    {
        $this->context = $context;
    }

    /**
     * @return string|null
     */
    public function getBaseword(): ?string
    {
        return $this->baseword;
    }

    /**
     * @param string|null $baseword
     */
    public function setBaseword(?string $baseword): void
    {
        $this->baseword = $baseword;
    }

    /**
     * @return int
     */
    public function getMetaphone(): int
    {
        return $this->metaphone;
    }

    /**
     * @param int $metaphone
     */
    public function setMetaphone(int $metaphone): void
    {
        $this->metaphone = $metaphone;
    }

    /**
     * @return bool
     */
    public function isStopword(): bool
    {
        return $this->isStopword;
    }

    /**
     * @param bool $isStopWord
     */
    public function setIsStopword(bool $isStopWord): void
    {
        $this->isStopword = $isStopWord;
    }

    /**
     * @return bool
     */
    public function isGenerated(): bool
    {
        return $this->isGenerated;
    }

    /**
     * @param bool $isGenerated
     */
    public function setIsGenerated(bool $isGenerated): void
    {
        $this->isGenerated = $isGenerated;
    }

    /**
     * @return int
     */
    public function getOccurrence(): int
    {
        return $this->occurrence;
    }

    /**
     * @param int $occurrence
     */
    public function setOccurrence(int $occurrence): void
    {
        $this->occurrence = $occurrence;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

}
