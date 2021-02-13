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
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Umsetzung
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_implementation")
 * @ORM\HasLifecycleCallbacks
 * @ApiResource
 */
class Implementation extends BaseNamedEntity
{

    /**
     * umtzgfinal (ja/nein)
     * @var bool
     *
     * @ORM\Column(name="is_final", type="boolean")
     */
    protected $isFinal = false;

    /**
     * geplant (ja/nein)
     * @var bool
     *
     * @ORM\Column(name="is_planned", type="boolean")
     */
    protected $isPlanned = false;
    /*
    fim (ja/nein)
    steckbr (ja/nein)
    fimurl
     */
}
