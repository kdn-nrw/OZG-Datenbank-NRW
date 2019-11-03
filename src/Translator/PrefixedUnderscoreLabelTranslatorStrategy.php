<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-08-15
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

    private $prefix;

    public function setAdminClass(string $adminClassName)
    {
        if (preg_match('/^((.*Bundle|[^\\\\]+)?(\\\\.+)*\\\\)?([^\\\\]+)$/', $adminClassName, $matches) === 1) {
            $bundle = preg_replace('/^(.*)Bundle$/', '$1', str_replace('\\', '', $matches[2]));
            $class = preg_replace('/^(.*)Admin$/', '$1', $matches[4]);
            $snakeCaseConverter = new SnakeCaseConverter();
            $prefix = empty($bundle) ? $class : $bundle . '\\' . $class;
            $prefix = $snakeCaseConverter->classNameToSnakeCase($prefix) . '.';
            $this->prefix = $prefix;
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
        switch ($context) {
            case 'form':
            case 'list':
            case 'filter':
            case 'show':
                $context = 'entity';
                break;
            case 'breadcrumb':
                $context = '';
                break;
        }
        $label = str_replace('.', '_', $label);
        $filteredLabel = strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $label));
        $key = '';
        if (!empty($context)) {
            $key .= $context . '.';
        }
        if ($type == 'label' || $type == 'link') {
            $type = '';
        }
        if (!empty($type)) {
            $key .= $type . '_';
        }
        $key .= $filteredLabel;
        return ($this->prefix ? $this->prefix : '') . $key;
    }
}
