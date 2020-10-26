<?php

declare(strict_types=1);

/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Type;

use App\Util\SnakeCaseConverter;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecurityRolesType extends AbstractTypeExtension
{
    /**
     * @var array
     */
    private $bundles;

    public function __construct(KernelInterface $kernel)
    {
        $this->bundles = array_keys($kernel->getBundles());
    }

    private function getTransposedBundles()
    {
        $bundles = [];
        foreach ($this->bundles as $bundle) {
            $bundles[$bundle] = strtoupper(SnakeCaseConverter::classNameToSnakeCase(preg_replace('/Bundle$/', '', $bundle)));
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $bundles = $this->getTransposedBundles();
        $bundlesPattern = '/('.str_replace('_', '_?', implode('|',$bundles)).')/';

        $resolver->setDefault('choice_attr',  function($choice, $key, $value) use ($bundlesPattern) {
            // adds a class like attending_yes, attending_no, etc
            if (preg_match('/: (.*)$/', $key, $matches) === 1) {
                $subRoles = explode(', ', $matches[1]);
            } else {
                $subRoles = '';
            }
            if (preg_match('/_([^_]+\\\\[^_]+)_/', $value, $matches) === 1) {
                $group = $matches[1];
            } elseif (preg_match($bundlesPattern, $value, $matches) === 1) {
                $group = $matches[1];
            } else {
                $group = '_DEFAULT_';
            }
            return [
                'data-role-matrix-key'   => $key,
                'data-role-matrix-group' => $group,
                'data-sub-roles'         => json_encode($subRoles),
            ];
        });
        $resolver->setDefault('choice_label',  function($choice, $key, $value) {
            // adds a class like attending_yes, attending_no, etc
            return $choice;
        });
        $resolver->setDefault('attr',  ['data-roles-matrix' => md5(spl_object_hash($this))]);
    }

    /**
     * Return the class of the type being extended.
     */
    public static function getExtendedTypes(): iterable
    {
        // return FormType::class to modify (nearly) every field in the system
        return [\Sonata\UserBundle\Form\Type\SecurityRolesType::class];
    }
}
