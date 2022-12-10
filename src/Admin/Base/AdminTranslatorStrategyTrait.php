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

namespace App\Admin\Base;


use App\Translator\PrefixedUnderscoreLabelTranslatorStrategy;
use Sonata\AdminBundle\Translator\LabelTranslatorStrategyInterface;

/**
 * Class AdminTranslatorStrategyTrait
 *
 * @property LabelTranslatorStrategyInterface $labelTranslatorStrategy
 * @method setLabelTranslatorStrategy(LabelTranslatorStrategyInterface $labelTranslatorStrategy): void
 * @method getLabelTranslatorStrategy(): LabelTranslatorStrategyInterface
 */
trait AdminTranslatorStrategyTrait
{
    /**
     * @var string|null
     */
    protected $translatorNamingPrefix;

    /**
     * @var string[]
     */
    protected $customLabels = [];


    final public function configureAppTranslatorStrategy(): LabelTranslatorStrategyInterface
    {
        try {
            $translatorStrategy = $this->getLabelTranslatorStrategy();
        } catch (\Exception $e) {
            $translatorStrategy = null;
        }
        if (!($translatorStrategy instanceof PrefixedUnderscoreLabelTranslatorStrategy)) {
            $translatorStrategy = new PrefixedUnderscoreLabelTranslatorStrategy();
            $this->setLabelTranslatorStrategy($translatorStrategy);
        }
        $translatorStrategy->setAdminClass($this->getTranslatorNamingPrefix(), $this->customLabels);
        return $translatorStrategy;
    }

    final protected function getAppLabelTranslatorStrategy(): LabelTranslatorStrategyInterface
    {
        return $this->configureAppTranslatorStrategy();
    }

    /**
     * @return string|null
     */
    protected function getTranslatorNamingPrefix(): ?string
    {
        if (null === $this->translatorNamingPrefix) {
            $this->translatorNamingPrefix = get_class($this);
        }
        return $this->translatorNamingPrefix;
    }
}
