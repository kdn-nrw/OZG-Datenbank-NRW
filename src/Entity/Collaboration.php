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
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Collaboration
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_collaboration")
 * @ORM\HasLifecycleCallbacks
 */
class Collaboration extends BaseNamedEntity
{

}
