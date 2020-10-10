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

use App\Entity\Base\BaseNamedEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Ministerium Bund
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_ministry_country")
 * @ORM\HasLifecycleCallbacks
 */
class MinistryCountry extends BaseNamedEntity
{
}
