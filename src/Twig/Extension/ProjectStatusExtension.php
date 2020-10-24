<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig\Extension;

use App\Entity\ImplementationProject;
use App\Service\ImplementationProjectHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProjectStatusExtension extends AbstractExtension
{

    /**
     * @var ImplementationProjectHelper
     */
    protected $implementationProjectHelper;

    /**
     * @param ImplementationProjectHelper $implementationProjectHelper
     */
    public function __construct(ImplementationProjectHelper $implementationProjectHelper)
    {
        $this->implementationProjectHelper = $implementationProjectHelper;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('app_implementation_project_status_info', [$this, 'getImplementationProjectStatusInfo']),
        ];
    }

    /**
     * Returns the field description collection for the referenced fields
     *
     * @param ImplementationProject $object
     * @return array
     */
    public function getImplementationProjectStatusInfo(ImplementationProject $object): array
    {
        return $this->implementationProjectHelper->getProjectStatusInfo($object);
    }
}
