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

namespace App\Api\Form\Type;

use App\Api\Consumer\ApiConsumerInterface;
use App\Util\SnakeCaseConverter;
use ReflectionClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ApiConsumerSearchType
 */
class ApiConsumerSearchType extends AbstractType
{
    /**
     * @var ApiConsumerInterface
     */
    protected $provider;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->provider = $options['apiProvider'];
        $propertyConfiguration = $this->provider->getPropertyConfiguration();
        $reflectionClass = new ReflectionClass($this->provider);
        $groupName = str_replace(['Provider', 'Consumer'], '', $reflectionClass->getShortName());
        $filteredPrefix = SnakeCaseConverter::classNameToSnakeCase($groupName);
        $labelPrefix = 'app.api.' . $filteredPrefix . '.';
        foreach ($propertyConfiguration as $property => $configuration) {
            if (!$configuration->isCustomProperty()) {
                $labelKey = $labelPrefix . SnakeCaseConverter::classNameToSnakeCase($property);
                switch ($configuration->getDataType()) {
                    default:
                        $builder->add($property, TextType::class, [
                            'label' => $labelKey,
                            'required' => $configuration->isRequired(),
                            'attr' => [
                                'placeholder' => $labelKey,
                            ]
                        ]);
                }
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'apiProvider' => null,
        ]);
    }
}
