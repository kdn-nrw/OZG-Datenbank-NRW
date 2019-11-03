<?php
/**
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-03
 */

namespace App\Admin;


use App\Translator\PrefixedUnderscoreLabelTranslatorStrategy;
use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;

/**
 * Class AbstractAppAdmin
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2019 Gert Hammes
 * @since     2019-11-03
 * @property LabelTranslatorStrategyInterface $labelTranslatorStrategy
 */
trait AdminTranslatorStrategyTrait
{
    /**
     * @var string|null
     */
    protected $translatorNamingPrefix;

    public function getLabelTranslatorStrategy()
    {
        if ($this->labelTranslatorStrategy instanceof PrefixedUnderscoreLabelTranslatorStrategy) {
            $this->labelTranslatorStrategy->setAdminClass($this->getTranslatorNamingPrefix());
        }
        return $this->labelTranslatorStrategy;
    }

    /**
     * @return mixed
     */
    public function getTranslatorNamingPrefix()
    {
        if (null === $this->translatorNamingPrefix) {
            $this->translatorNamingPrefix = get_class($this);
        }
        return $this->translatorNamingPrefix;
    }
}
