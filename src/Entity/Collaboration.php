<?php

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Server
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_collaboration")
 * @ORM\HasLifecycleCallbacks
 */
class Collaboration extends BaseNamedEntity
{

}
