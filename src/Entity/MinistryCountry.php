<?php

namespace App\Entity;

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
