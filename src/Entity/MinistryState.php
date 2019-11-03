<?php

namespace App\Entity;

use App\Entity\Base\BaseNamedEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Ministerium Land
 *
 * @ORM\Entity
 * @ORM\Table(name="ozg_ministry_state")
 * @ORM\HasLifecycleCallbacks
 */
class MinistryState extends BaseNamedEntity
{
}
