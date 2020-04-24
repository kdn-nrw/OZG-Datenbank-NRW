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
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
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

    public function setAdminClass(string $adminClassName, $customLabels = [])
    {
        if (preg_match('/^((.*Bundle|[^\\\\]+)?(\\\\.+)*\\\\)?([^\\\\]+)$/', $adminClassName, $matches) === 1) {
            $bundle = preg_replace('/^(.*)Bundle$/', '$1', str_replace('\\', '', $matches[2]));
            $class = preg_replace('/^(.*)Admin$/', '$1', $matches[4]);
            $snakeCaseConverter = new SnakeCaseConverter();
            $prefix = empty($bundle) ? $class : $bundle . '\\' . $class;
            $filteredPrefix = $snakeCaseConverter->classNameToSnakeCase($prefix);
            $parts = explode('.', $filteredPrefix);
            unset($parts[0]);
            $this->adminKey = implode('_', $parts);
            $prefix = $filteredPrefix . '.';
            $this->prefix = $prefix;
            $this->customLabels[$prefix] = $customLabels;
        }
    }

    /**
     * @param string $label
     * @param string $context
     * @param string $type
     *
     * @return string
     */
    public function getLabel($label, $context = '', $type = '')
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
