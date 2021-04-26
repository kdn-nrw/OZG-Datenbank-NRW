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

namespace App\Service\Mailer;

trait InjectMarkerServiceTrait
{
    /**
     * @var MarkerService
     */
    protected $markerService;

    /**
     * @required
     * @param MarkerService $markerService
     */
    public function injectMarkerService(MarkerService $markerService): void
    {
        $this->markerService = $markerService;
    }
}