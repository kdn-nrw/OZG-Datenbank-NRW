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

namespace App\Controller;

use Sonata\AdminBundle\Controller\CRUDController;

/**
 * Class SpecializedProcedureAdminController
 *
 */
class SpecializedProcedureAdminController extends CRUDController
{
    use ControllerDownloadTrait;
}
