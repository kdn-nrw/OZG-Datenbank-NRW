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
    public const CSS_CLASS_DEFAULT = 'state-default';
    public const CSS_CLASS_GREEN = 'state-green';
    public const CSS_CLASS_ORANGE = 'state-orange';
    public const CSS_CLASS_RED = 'state-red';

    public const CSS_CLASS_CHOICES = [
        self::CSS_CLASS_DEFAULT => 'app.status.entity.css_class_choices.default',
        self::CSS_CLASS_GREEN => 'app.status.entity.css_class_choices.green',
        self::CSS_CLASS_ORANGE => 'app.status.entity.css_class_choices.orange',
        self::CSS_CLASS_RED => 'app.status.entity.css_class_choices.red',
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
