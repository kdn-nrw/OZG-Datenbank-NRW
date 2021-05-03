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

namespace App\Admin\Traits;

use App\Entity\Base\SluggableInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Trait SluggableTrait
 */
trait SluggableTrait
{
    protected function addSlugFormField(FormMapper $formMapper, ?object $subject): void
    {
        if ($subject instanceof SluggableInterface) {
            $formMapper
                ->add('slug', TextType::class, [
                    'label' => 'app.common.fields.slug',
                    'required' => false,
                ]);
        };
    }
}