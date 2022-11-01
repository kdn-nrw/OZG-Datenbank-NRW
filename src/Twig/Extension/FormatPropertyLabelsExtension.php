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

namespace App\Twig\Extension;

use App\Entity\Base\BaseEntityInterface;
use App\Translator\PrefixedUnderscoreLabelTranslatorStrategy;
use App\Translator\TranslatorAwareTrait;
use App\Util\SnakeCaseConverter;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FormatPropertyLabelsExtension extends AbstractExtension
{
    use TranslatorAwareTrait;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('app_get_object_property_label_key', [$this, 'getObjectPropertyLabelKey']),
        ];
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFilters()
    {
        return [
            new TwigFilter('app_format_property_labels', [$this, 'getObjectPropertyLabels']),
        ];
    }


    /**
     * @param object $object The object
     * @param string $property The property
     * @param ?string $customPrefix Optional custom label prefix
     * @return string
     */
    public function getObjectPropertyLabelKey(object $object, string $property, ?string $customPrefix = null): string
    {
        if ($customPrefix) {
            return rtrim($customPrefix, '.') . '.' . SnakeCaseConverter::camelCaseToSnakeCase($property);
        }
        $group = $object instanceof BaseEntityInterface ? 'entity' : 'model';
        $labelPrefix = PrefixedUnderscoreLabelTranslatorStrategy::getClassLabelPrefix(get_class($object), $group);
        return $labelPrefix . SnakeCaseConverter::camelCaseToSnakeCase($property);
    }


    /**
     * @param BaseEntityInterface $object The object
     * @param array $propertyList The property list to be translated
     * @return string
     */
    public function getObjectPropertyLabels(BaseEntityInterface $object, array $propertyList): string
    {
        $labelPrefix = PrefixedUnderscoreLabelTranslatorStrategy::getClassLabelPrefix(get_class($object));
        $sentenceParts = [];
        foreach ($propertyList as $property) {
            if (is_array($property)) {
                $subParts = [];
                foreach ($property as $orProperty) {
                    $subParts[] = $this->translate($labelPrefix . SnakeCaseConverter::camelCaseToSnakeCase($orProperty));
                }
                $sentenceParts[] = implode(' oder ', $subParts);
            } else {
                $sentenceParts[] = $this->translate($labelPrefix . SnakeCaseConverter::camelCaseToSnakeCase($property));
            }
        }
        return implode(', ', $sentenceParts);
    }
}
