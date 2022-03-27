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
            $this->translatorNamingPrefix = get_class($this);
        }
        return $this->translatorNamingPrefix;
    }
}
