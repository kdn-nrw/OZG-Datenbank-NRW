<?php

namespace App\Translator;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Translation trait
 */
trait TranslatorAwareTrait
{

    /**
     * Translator
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Helper function for translations; sets the configured translator text
     * domain so the translations from the current module will be used
     * unless the text domain is overridden
     *
     * @param string $message Message to be translated
     * @param string $textDomain (Optional) Translator text domain
     * @param array  $parameters An array of parameters for the message
     *
     * @return string
     */
    final protected function translate($message, $textDomain = null, array $parameters = array()): string
    {
        /*$textValue = $message;
        if ($textDomain === null) {
            $textDomain = $this->getTranslatorTextDomain();
        }*/

        $textValue = $this->getTranslator()->trans($message, $parameters, $textDomain);
        //trans($id, array $parameters = array(), $domain = null, $locale = null)

        return $textValue;
    }

    /**
     * @param TranslatorInterface $translator
     */
    final public function setTranslator($translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @return TranslatorInterface
     */
    final protected function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }
}
