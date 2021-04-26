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

namespace App\Translator;


use App\Util\SnakeCaseConverter;
use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;

/**
 * Class PrefixedUnderscoreLabelTranslatorStrategy
 */
class PrefixedUnderscoreLabelTranslatorStrategy implements LabelTranslatorStrategyInterface
{
    private static $commonTranslations = [
        'id' => 'app.common.fields.id',
        'createdAt' => 'app.common.fields.created_at',
        'modifiedAt' => 'app.common.fields.modified_at',
        'hidden' => 'app.common.fields.hidden',
        'createdBy' => 'app.common.fields.created_by',
        'modifiedBy' => 'app.common.fields.modified_by',
    ];

    private $adminKey;
    private $prefix;

    /**
     * Custom field labels
     *
     * @var array
     */
    private $customLabels = [];

    /**
     * @var array|string[]
     */
    protected static $classLabelPrefixCache = [];

    /**
     * Initializes the translator settings from the given admin class
     *
     * @param string $adminClassName
     * @param array $customLabels
     */
    public function setAdminClass(string $adminClassName, $customLabels = []): void
    {
        if (preg_match('/^((.*Bundle|[^\\\\]+)?(\\\\.+)*\\\\)?([^\\\\]+)$/', $adminClassName, $matches) === 1) {
            $bundle = preg_replace('/^(.*)Bundle$/', '$1', str_replace('\\', '', $matches[2]));
            $class = preg_replace('/^(.*)Admin$/', '$1', $matches[4]);
            $prefix = empty($bundle) ? $class : $bundle . '\\' . $class;
            $filteredPrefix = SnakeCaseConverter::classNameToSnakeCase($prefix);
            $parts = explode('.', $filteredPrefix);
            unset($parts[0]);
            $this->adminKey = implode('_', $parts);
            $prefix = $filteredPrefix . '.';
            $this->prefix = $prefix;
            $this->customLabels[$prefix] = $customLabels;
        }
    }

    /**
     * Creates the label key for the given entity class and property
     *
     * @param string $entityClass The fully qualified entity class name
     * @param string $property A property name
     * @return string
     */
    public static function getClassPropertyLabel(string $entityClass, string $property = ''): string
    {
        if (!empty($property)) {
            return self::getClassLabelPrefix($entityClass) . SnakeCaseConverter::camelCaseToSnakeCase($property);
        }
        return self::getClassLabelPrefix($entityClass, '') . 'list';
    }

    /**
     * Creates the label prefix for the entity class with the optional translation (label key) group
     *
     * @param string $entityClass
     * @param string $group Optional label key group
     * @return string
     */
    public static function getClassLabelPrefix(string $entityClass, string $group = 'entity'): string
    {
        if (!array_key_exists($entityClass, self::$classLabelPrefixCache)) {
            $classParts = explode('\\', $entityClass);
            $lastIndex = count($classParts) - 1;
            $className = $classParts[$lastIndex];
            unset($classParts[$lastIndex]);
            $vendor = strtolower($classParts[0]);
            if ($vendor !== 'app') {
                $vendor .= '_' . str_replace('Bundle', '', $classParts[1]);
            }
            //$prefix = strtolower($classParts[0]) === 'app' ? 'app.' : implode('.', $classParts) . '.';
            self::$classLabelPrefixCache[$entityClass] = $vendor . '.' . $className;
        }
        if ($group) {
            $append = '.' . $group;
        } else {
            $append = '';
        }
        return SnakeCaseConverter::classNameToSnakeCase(self::$classLabelPrefixCache[$entityClass] . $append) . '.';
    }

    /**
     * @param string $label
     * @param string $context
     * @param string $type
     *
     * @return string
     */
    public function getLabel($label, $context = '', $type = ''): string
    {
        if (array_key_exists($label, self::$commonTranslations)) {
            return self::$commonTranslations[$label];
        }
        $label = str_replace('.', '_', $label);
        $filteredLabel = strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $label));
        if ($this->adminKey) {
            $actionLabels = ['list', 'show', 'create', 'edit'];
            foreach ($actionLabels as $action) {
                if (strpos($filteredLabel, $this->adminKey . '_' . $action) === 0) {
                    $filteredLabel = $action;
                    break;
                }
            }
        }
        if ($this->adminKey && strpos($filteredLabel, $this->adminKey . '_' . $type) === 0) {
            $filteredLabel = str_replace($this->adminKey . '_', '', $filteredLabel);
        }
        switch ($context) {
            case 'form':
            case 'list':
            case 'filter':
            case 'show':
            case 'export':
                $context = 'entity';
                break;
            case 'breadcrumb':
                $context = '';
                break;
        }
        $key = '';
        if (!empty($context)) {
            $key .= $context . '.';
        }
        if ($type === 'label' || $type === 'link') {
            $type = '';
        }
        if (!empty($type)) {
            $key .= $type . '_';
        }
        $key .= $filteredLabel;
        if ($this->prefix && strpos($key, $this->prefix) !== 0) {
            $key = $this->prefix . $key;
        }
        if ($this->prefix && isset($this->customLabels[$this->prefix][$key])) {
            return $this->customLabels[$this->prefix][$key];
        }
        return $key;
    }
}
