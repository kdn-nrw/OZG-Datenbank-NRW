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

namespace App\Entity\Base;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;


/**
 * Trait PersonPropertiesTrait
 */
trait PersonPropertiesTrait
{
    /**
     * Available choices for genders
     *
     * @var string[]
     */
    public static $genderTypeChoices = [
        PersonInterface::GENDER_MALE => 'app.contact.entity.gender_choices.male',
        PersonInterface::GENDER_FEMALE => 'app.contact.entity.gender_choices.female',
        PersonInterface::GENDER_OTHER => 'app.contact.entity.gender_choices.other',
        PersonInterface::GENDER_UNKNOWN => 'app.contact.entity.gender_choices.unknown',
    ];

    /**
     * @ORM\Column(type="integer", name="gender", nullable=true)
     * @var int|null
     */
    private $gender = PersonInterface::GENDER_UNKNOWN;

    /**
     * @ORM\Column(type="string", name="title", length=100, nullable=true)
     * @var string|null
     */
    private $title;

    /**
     * @ORM\Column(type="string", name="first_name", length=100, nullable=true)
     * @var string|null
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", name="last_name", length=100, nullable=true)
     * @var string|null
     */
    private $lastName;

    /**
     * @return int
     */
    public function getGender(): int
    {
        if (!isset(self::$genderTypeChoices[$this->gender])) {
            return PersonInterface::GENDER_UNKNOWN;
        }
        return $this->gender;
    }

    /**
     * @param string|int|null $gender
     */
    public function setGender($gender): void
    {
        switch ($gender) {
            case User::GENDER_MALE:
            case PersonInterface::GENDER_MALE:
                $this->gender = PersonInterface::GENDER_MALE;
                break;
            case User::GENDER_FEMALE:
            case PersonInterface::GENDER_FEMALE:
                $this->gender = PersonInterface::GENDER_FEMALE;
                break;
            case User::GENDER_UNKNOWN:
            case PersonInterface::GENDER_OTHER:
                $this->gender = PersonInterface::GENDER_OTHER;
                break;
            default:
                $this->gender = PersonInterface::GENDER_UNKNOWN;
        }
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     * @return self
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     * @return self
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     * @return self
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName(): string
    {
        $name = trim($this->getFirstName() . ' ' . $this->getLastName());
        if ($name) {
            $title = $this->getTitle();
            if ($title) {
                $name = $title . ' ' . $name;
            }
        }
        return $name;
    }
}
