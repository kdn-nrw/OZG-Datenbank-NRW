<?php

namespace App\Statistics;

use App\Translator\TranslatorAwareTrait;
use App\Statistics\Options\ExportOptions;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Excel writer for statistics providers
 */
class ExportHelper
{
    use TranslatorAwareTrait;

    /**
     * Constructor
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TranslatorInterface $translator
    )
    {
        $this->setTranslator($translator);
    }

    /**
     * Generate an array with translated header fields
     *
     * @param ExportOptions $exportOptions
     *
     * @return array of header fields
     */
    public function generateHeaders(ExportOptions $exportOptions)
    {
        $visibleFields = $exportOptions->getVisibleFields();
        $headerLabels = $exportOptions->getHeaderLabels();
        $translationPrefix = $exportOptions->getTranslationPrefix();

        $headers = [];
        $translator = $this->getTranslator();
        foreach ($visibleFields as $visibleField) {
            if (strpos($visibleField, ' ') !== false) {
                $translatedField = $visibleField;
            } elseif (array_key_exists($visibleField, $headerLabels)) {
                $translatedField = $translator->trans($headerLabels[$visibleField]);
            } else {
                $visibleFieldUnderscore = $this->formatCamelCaseToUnderscore($visibleField);
                $translationKey = $translationPrefix . '.' . $visibleFieldUnderscore;
                $translatedField = $translator->trans($translationKey);
            }
            $headers[] = $translatedField;
        }
        return $headers;
    }

    /**
     * @param string $value
     * @return string
     */
    private function formatCamelCaseToUnderscore($value): string
    {
        return strtolower(ltrim(preg_replace('/[A-Z]/', '_$0', $value), '_'));
    }
}