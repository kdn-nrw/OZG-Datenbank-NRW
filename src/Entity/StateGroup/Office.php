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

namespace App\Entity\StateGroup;

use App\Entity\Base\BaseBlamableEntity;
use App\Entity\Base\HideableEntityInterface;
use App\Entity\Base\HideableEntityTrait;
use App\Entity\Base\NamedEntityInterface;
use App\Entity\Base\NamedEntityTrait;
use App\Entity\ContactTextTrait;
use App\Entity\UrlTrait;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Office
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_office")
 * @ORM\HasLifecycleCallbacks
 */
class Office extends BaseBlamableEntity implements NamedEntityInterface, HideableEntityInterface
{
    use NamedEntityTrait;
    use HideableEntityTrait;
    use ContactTextTrait;
    use UrlTrait;

    /**
     * ozgrgbeschreibung
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description = '';

    /**
     * @var Commune
     * @ORM\ManyToOne(targetEntity="App\Entity\StateGroup\Commune", inversedBy="offices", cascade={"persist"})
     */
    private $commune;

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
     * @return Commune
     */
    public function getCommune(): Commune
    {
        return $this->commune;
    }

    /**
     * @param Commune $commune
     */
    public function setCommune($commune): void
    {
        $this->commune = $commune;
    }
}
