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

namespace App\Statistics\Options;

/**
 * ExportOptions
 */
class ExportOptions
{
    /**
     * @var string[]
     */
    private $visibleFields;
    /**
     * @var string[]
     */
    private $headerLabels;
    /**
     * @var string
     */
    private $translationPrefix;
    /**
     * @var string
     */
    private $eventPrefix = '';
    /**
     * @var string[]
     */
    private $fileMetaData;
    /**
     * @var float
     */
    private $columnWidthMax;
    /**
     * @var float
     */
    private $columnWidthMin;

    /**
     * ExportOptions constructor.
     * @param array $actionOptions
     * @throws \Exception
     */
    public function __construct(array $actionOptions)
    {
        $this->fileMetaData = $actionOptions['meta_data'];
        $viewParameters = $actionOptions['view_parameters'];
        if (empty($viewParameters['visible_fields'])
            || empty($viewParameters['translation_prefix'])) {
            $msg = 'Please set the view parameters "visible_fields" and "translation_prefix"'
                . ' in the controller options section "export_excel"';
            throw new \Exception($msg);
        }
        $this->visibleFields = $viewParameters['visible_fields'];
        if (isset($viewParameters['header_labels'])) {
            $this->headerLabels = $viewParameters['header_labels'];
        } else {
            $this->headerLabels = [];
        }
        $this->translationPrefix = $viewParameters['translation_prefix'];
        if (isset($viewParameters['event_prefix'])) {
            $this->eventPrefix = $viewParameters['event_prefix'];
        }
        if (!empty($viewParameters['column_widths']['max'])) {
            $this->columnWidthMax = $viewParameters['column_widths']['max'];
        } else {
            $this->columnWidthMax = 30;
        }
        if (!empty($viewParameters['column_widths']['min'])) {
            $this->columnWidthMin = $viewParameters['column_widths']['min'];
        } else {
            $this->columnWidthMin = 8;
        }
    }

    /**
     * @return string[]
     */
    public function getVisibleFields()
    {
        return $this->visibleFields;
    }

    /**
     * @param string[] $visibleFields
     * @return ExportOptions
     */
    public function setVisibleFields($visibleFields)
    {
        $this->visibleFields = $visibleFields;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getHeaderLabels()
    {
        return $this->headerLabels;
    }

    /**
     * @param string[] $headerLabels
     * @return ExportOptions
     */
    public function setHeaderLabels($headerLabels)
    {
        $this->headerLabels = $headerLabels;
        return $this;
    }

    /**
     * @return string
     */
    public function getTranslationPrefix()
    {
        return $this->translationPrefix;
    }

    /**
     * @param string $translationPrefix
     * @return ExportOptions
     */
    public function setTranslationPrefix($translationPrefix)
    {
        $this->translationPrefix = $translationPrefix;
        return $this;
    }

    /**
     * @return string
     */
    public function getEventPrefix()
    {
        return $this->eventPrefix;
    }

    /**
     * @param string $eventPrefix
     * @return ExportOptions
     */
    public function setEventPrefix($eventPrefix)
    {
        $this->eventPrefix = $eventPrefix;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getFileMetaData()
    {
        return $this->fileMetaData;
    }

    /**
     * @param string[] $fileMetaData
     * @return ExportOptions
     */
    public function setFileMetaData($fileMetaData)
    {
        $this->fileMetaData = $fileMetaData;
        return $this;
    }

    /**
     * @return float
     */
    public function getColumnWidthMax()
    {
        return $this->columnWidthMax;
    }

    /**
     * @param float $columnWidthMax
     * @return ExportOptions
     */
    public function setColumnWidthMax($columnWidthMax)
    {
        $this->columnWidthMax = $columnWidthMax;
        return $this;
    }

    /**
     * @return float
     */
    public function getColumnWidthMin()
    {
        return $this->columnWidthMin;
    }

    /**
     * @param float $columnWidthMin
     * @return ExportOptions
     */
    public function setColumnWidthMin($columnWidthMin)
    {
        $this->columnWidthMin = $columnWidthMin;
        return $this;
    }
}