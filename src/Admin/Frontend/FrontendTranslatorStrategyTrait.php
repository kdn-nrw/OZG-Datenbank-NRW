<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-09
 */

namespace App\Admin\Frontend;


use App\Translator\PrefixedUnderscoreLabelTranslatorStrategy;
use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;

/**
 * Class FrontendTranslatorStrategyTrait
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-09
 * @property LabelTranslatorStrategyInterface $labelTranslatorStrategy
 * @property array $customLabels
 */
trait FrontendTranslatorStrategyTrait
{
    /**
     * @var string|null
     */
    protected $translatorNamingPrefix;

    /**
     * Custom labels for entity fields. Required for sub-entity fields
     * Example: 'entity.service_system_situation' => 'app.service_system.entity.situation',
     * @var string[]
     */
    protected $customLabels = [];

    public function getLabelTranslatorStrategy()
    {
        if ($this->labelTranslatorStrategy instanceof PrefixedUnderscoreLabelTranslatorStrategy) {
            $this->labelTranslatorStrategy->setAdminClass($this->getTranslatorNamingPrefix(), $this->customLabels);
        }
        return $this->labelTranslatorStrategy;
    }

    /**
     * @return mixed
     */
    public function getTranslatorNamingPrefix()
    {
        if (null === $this->translatorNamingPrefix) {
            $this->translatorNamingPrefix = str_replace('\\Frontend', '', get_class($this));
        }
        return $this->translatorNamingPrefix;
    }
}
