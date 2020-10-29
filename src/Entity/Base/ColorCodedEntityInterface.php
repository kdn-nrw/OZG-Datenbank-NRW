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

namespace App\Entity\Base;

/**
 * ColorCodedEntityInterface interface
 */
interface ColorCodedEntityInterface
{
    public const CSS_CLASS_CHOICES = [
        'bg-primary',
        'bg-green',
        'bg-red',
        'bg-orange',
        'bg-blue',
        'bg-success',
        'bg-warning',
        'bg-danger',
        'bg-info',
        'bg-light-green-300',
        'bg-light-green-700',
        'bg-red-300',
        'bg-red-700',
        'bg-yellow-300',
        'bg-yellow-700',
        'bg-amber-300',
        'bg-amber-700',
        'bg-blue-gray-300',
        'bg-blue-gray-700',
        'bg-gray-300',
        'bg-gray-700',
    ];

    /**
     * @return string|null
     */
    public function getColor(): ?string;

    /**
     * @return string|null
     */
    public function getCssClass(): ?string;
}
