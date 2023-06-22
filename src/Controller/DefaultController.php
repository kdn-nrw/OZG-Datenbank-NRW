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

namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DefaultController
 */
class DefaultController extends AbstractController
{

    public function indexAction()
    {
        $redirectRoute = 'sonata_admin_dashboard';
        if (!empty($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'ozg.kdn.de') {
            $redirectRoute = 'frontend_app_modelregion_list';
        }
        $redirectParams = [];
        if ((null !== $user = $this->getUser()) && $this->isGranted('ROLE_SHOW_BACKEND')) {
            if ($user instanceof User) {
                $rolePrefix = 'ROLE_APP\\ADMIN\\STATEGROUP\\COMMUNEADMIN_';
                if (($this->isGranted($rolePrefix . 'ALL') || $this->isGranted($rolePrefix . 'VIEW'))
                    && $user->getCommunes()->count() === 1) {
                    $redirectRoute = 'admin_app_stategroup_commune_show';
                    $redirectParams = ['id' => $user->getCommunes()->first()->getId()];
                } else {
                    $rolePrefix = 'ROLE_APP\\ADMIN\\MODELREGIONADMIN_';
                    if (($this->isGranted($rolePrefix . 'ALL') || $this->isGranted($rolePrefix . 'VIEW'))
                        && $user->getModelRegions()->count() === 1) {
                        $redirectRoute = 'admin_app_modelregion_show';
                        $redirectParams = ['id' => $user->getModelRegions()->first()->getId()];
                    }
                }
            }
        }
        return $this->redirectToRoute($redirectRoute, $redirectParams, 302);
    }
}
