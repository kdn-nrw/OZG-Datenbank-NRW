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

namespace App\Entity\Onboarding;

use App\Entity\AddressTrait;
use App\Entity\Base\ContactPropertiesTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Release
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_onboarding_release")
 */
class Release extends AbstractOnboardingEntity
{
    use AddressTrait;
    use ContactPropertiesTrait;

    /**
     * @var array Supported release states
     */
    public static $releaseStatusChoices = [
        'app.release.entity.release_status_choices.offline' => self::STATUS_NEW,
        'app.release.entity.release_status_choices.planned' => self::STATUS_INCOMPLETE,
        'app.release.entity.release_status_choices.online' => self::STATUS_COMPLETE,
    ];

    /**
     * Has account mandator
     * @var int|null
     *
     * @ORM\Column(name="release_status", type="integer", nullable=true)
     */
    protected $releaseStatus;

    /**
     * @var null|DateTime
     *
     * @ORM\Column(nullable=true, type="datetime", name="release_date")
     */
    protected $releaseDate;

    /**
     * release confirmed
     *
     * @var bool
     *
     * @ORM\Column(name="release_confirmed", type="boolean", nullable=true)
     */
    protected $releaseConfirmed = false;

    /**
     * @return int|null
     */
    public function getReleaseStatus(): ?int
    {
        return $this->releaseStatus;
    }

    /**
     * @param int|null $releaseStatus
     */
    public function setReleaseStatus(?int $releaseStatus): void
    {
        $this->releaseStatus = $releaseStatus;
    }

    /**
     * @return DateTime|null
     */
    public function getReleaseDate(): ?DateTime
    {
        return $this->releaseDate;
    }

    /**
     * @param DateTime|null $releaseDate
     */
    public function setReleaseDate(?DateTime $releaseDate): void
    {
        $this->releaseDate = $releaseDate;
    }

    /**
     * @return bool
     */
    public function isReleaseConfirmed(): bool
    {
        return $this->releaseConfirmed;
    }

    /**
     * @param bool $releaseConfirmed
     */
    public function setReleaseConfirmed(bool $releaseConfirmed): void
    {
        $this->releaseConfirmed = $releaseConfirmed;
    }

    /**
     * @param int $completionRate
     */
    public function setCompletionRate(int $completionRate): void
    {
        if (null !== $this->releaseDate) {
            if (null === $this->releaseStatus) {
                $this->releaseStatus = self::STATUS_NEW;
            }
            if (!$this->releaseConfirmed) {
                $this->releaseStatus = self::STATUS_INCOMPLETE;
            } else {
                $this->releaseStatus = self::STATUS_COMPLETE;
            }
        } else {
            $this->releaseStatus = self::STATUS_NEW;
        }
        parent::setCompletionRate($completionRate);
    }

    /**
     * @return string
     */
    public function getReleaseStatusLabelKey(): string
    {
        $releaseStatus = $this->getReleaseStatus();
        if (null !== $releaseStatus && $key = array_search($releaseStatus, self::$releaseStatusChoices, true)) {
            return $key;
        }
        return array_search(self::STATUS_NEW, self::$releaseStatusChoices, true);
    }
}
