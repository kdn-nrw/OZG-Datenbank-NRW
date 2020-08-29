<?php
/**
 * This file is part of the KDN OZG package.
 *
 * @author    Gert Hammes <info@gerthammes.de>
 * @copyright 2020 Gert Hammes
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Search;

/**
 * Search text processor
 */
class TextProcessor
{
    /**
     * The maximum length for a single word in the index table
     *
     * @var int
     */
    private const MAX_WORD_LENGTH = 128;

    /**
     * Default minimum length of search words
     *
     * @var int
     */
    public const DEFAULT_MINIMUM_SEARCH_WORD_LENGTH = 3;

    /**
     * Create word list for given text
     *
     * @param string $indexText
     * @param array $matchPatterns Extra search patterns for allowed search words
     * @return array
     */
    public static function createWordListForText(string $indexText, array $matchPatterns = []): array
    {
        $fullTextSearchWords = [];
        if ($indexText !== '') {
            $filteredContent = self::filterContent($indexText);
            $lines = explode("\n", $filteredContent);
            foreach ($lines as $line) {
                if ('' !== $trimmedLine = trim($line)) {
                    self::addTextWords($trimmedLine, $fullTextSearchWords, $matchPatterns);
                }
            }
        }
        if (empty($matchPatterns)) {
            return self::filterContainingSearchWords($fullTextSearchWords);
        }
        return $fullTextSearchWords;
    }

    /**
     * Filters out search words that are contained in other search words
     *
     * @param array $unsortedSearchWords The complete list of search words
     *
     * @return array
     */
    private static function filterContainingSearchWords(array $unsortedSearchWords): array
    {
        $mapKeys = [];
        $keys = array_keys($unsortedSearchWords);
        foreach ($keys as $key) {
            $mapKeys[] = [
                'key' => $key,
                'length' => mb_strlen((string) $key),
            ];
        }
        uasort($mapKeys, static function ($a, $b) {
            if ($a['length'] === $b['length']) {
                if ($a['key'] === $b['key']) {
                    return 0;
                }
                return ($a['key'] < $b['key']) ? -1 : 1;
            }
            return ($a['length'] > $b['length']) ? -1 : 1;
        });
        $sortedSearchWords = [];
        foreach ($mapKeys as $keyMeta) {
            $key = $keyMeta['key'];
            $ftsKeys = array_keys($sortedSearchWords);
            $isAlreadyIncludedInWord = null;
            foreach ($ftsKeys as $addedWord) {
                if ($key !== $addedWord && mb_strpos((string) $addedWord, (string)$key) !== false) {
                    $isAlreadyIncludedInWord = $addedWord;
                    $sortedSearchWords[$addedWord]['count'] += $unsortedSearchWords[$key]['count'];
                    if (!isset($sortedSearchWords[$addedWord]['contains'])) {
                        $sortedSearchWords[$addedWord]['contains'] = [];
                    }
                    $sortedSearchWords[$addedWord]['contains'][$key] = $unsortedSearchWords[$key];
                    break;
                }
            }
            if (!$isAlreadyIncludedInWord) {
                $sortedSearchWords[$key] = $unsortedSearchWords[$key];
            }
        }
        return $sortedSearchWords;
    }

    /**
     * Add the words of the given (one-line) text to the word list
     *
     * @param string $indexText
     * @param array $fullTextSearchWords
     * @param array $extraMatchPatterns Extra search patterns for allowed search words
     */
    private static function addTextWords(
        string $indexText,
        array &$fullTextSearchWords,
        array $extraMatchPatterns = []
    ): void
    {
        $allowDefaultChars = '0-9a-zA-ZäöüÜÖÄß';
        $searchTerm = mb_strtolower(str_replace("\n", ' ', $indexText));
        $patternSearch = '/([' . $allowDefaultChars. ']+)([\.\-\s\/]*)/u';
        $patterns = $extraMatchPatterns;
        $patterns[] = $patternSearch;
        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $searchTerm, $matches);
            if (empty($matches[1])) {
                continue;
            }
            $wordMap = [];
            foreach ($matches[1] as $offset => $word) {
                $wordMap[$offset] = [
                    'word' => $word,
                    'after' => $matches[2][$offset],
                    'is_processed' => false,
                    'is_valid' => true,
                ];
            }
            $searchWordList = [];
            $offsets = array_keys($wordMap);
            foreach ($offsets as $offset) {
                if (!$wordMap[$offset]['is_processed']) {
                    self::addSearchMatch($searchWordList, $wordMap, $offset);
                }
            }
            foreach ($searchWordList as $searchWord) {
                self::addWord($searchWord, $fullTextSearchWords);
            }
        }
    }

    /**
     * Add the words to the list of search words
     * Adds special handling for compound words (multiple words separated only by . (for dates) or -)
     *
     * @param array $searchWords The list of search words
     * @param array $wordMap The mapping for the matched words
     * @param int $offset The word map offset
     * @param string $prefixText An optional prefix text
     */
    private static function addSearchMatch(array &$searchWords, array &$wordMap, int $offset = 0, string $prefixText = ''): void
    {
        $wordMeta =& $wordMap[$offset];
        $word = $wordMeta['word'];
        $wordLength = mb_strlen($word);
        $nextPrefixText = '';
        $minWordLength = self::DEFAULT_MINIMUM_SEARCH_WORD_LENGTH;
        $isValidStandalone = $wordLength >= $minWordLength;
        if (!empty($wordMap[$offset + 1])) {
            $afterWord = $wordMeta['after'];
            $isValidStandalone = $isValidStandalone && ($wordLength > 4 || !is_numeric($word));
            if ($afterWord === '.') {
                $nextPrefixText = $prefixText . $word . $afterWord;
            } elseif ($afterWord === '-') {
                $nextPrefixText = $prefixText . $word . $afterWord;
            } elseif ($afterWord === '/') {
                $nextPrefixText = $prefixText . $word . $afterWord;
            } elseif ($afterWord === ' ' && !$isValidStandalone) {
                if ($prefixText !== '' && ((mb_strlen(trim($prefixText)) > $minWordLength)
                    || rtrim($prefixText) === $prefixText)) {
                    $nextPrefixText = $prefixText . $word . $afterWord;
                }
            }
        }
        if ($isValidStandalone) {
            $searchWords[] = $word;
        } else {
            $wordMeta['is_valid'] = false;
        }
        $wordMeta['is_processed'] = true;
        // Only add word if it contains at least one valid word or if the term is completed
        if (!empty($prefixText) && ($isValidStandalone || $wordMap[$offset - 1]['is_valid'] || $nextPrefixText === '')) {
            $searchWords[] = $prefixText . $word;
        }
        if ($nextPrefixText !== '') {
            self::addSearchMatch($searchWords, $wordMap, $offset + 1, $nextPrefixText);
        }
    }

    /**
     * Filter urls in content (remove url parameters)
     *
     * @param string $content
     * @return string
     */
    private static function filterContent(string $content): string
    {
        $filteredContent = html_entity_decode($content, ENT_COMPAT, 'utf-8');
        $filteredContent = self::filterUrls($filteredContent);
        $filteredContent = preg_replace('/<th>[\wäöüÜÖÄß\s@:.\/\-]+<\/th>/u', '', $filteredContent);
        $filteredContent = preg_replace('/<!-- [\wäöüÜÖÄß\s@:.\/\-]+-->/u', '', $filteredContent);
        //$filteredContent = str_replace(['>', PHP_EOL, '?', '*'], ['> ', ' ', ' ', ' '], $filteredContent);
        $filteredContent = str_replace(['>', PHP_EOL], ['> ', ' '], $filteredContent);
        $filteredContent = trim(strip_tags($filteredContent));
        return $filteredContent;
    }

    /**
     * Filter urls in content (remove url parameters)
     *
     * @param string $indexText
     * @return string
     */
    private static function filterUrls(string $indexText): string
    {
        $pattern = '@((https?://)?([-\\w]+\\.[-\\w\\.]+)+\\w(:\\d+)?(/([-\\w/_\\.]*(\\?\\S+)?)?)*)@';
        preg_match_all($pattern, $indexText, $urlMatches);
        foreach ($urlMatches[0] as $url) {
            $urlParts = parse_url($url);
            if ($urlParts !== false && !empty($urlParts['host'])) {
                $urlPath = !empty($urlParts['path']) ? str_replace('.html', '', $urlParts['path']) : '';
                $indexText = str_replace($url, $urlParts['host'] . ' ' . $urlPath, $indexText);
            }
        }
        return $indexText;
    }

    /**
     * Adds the given word to the word list
     *
     * @param string $word
     * @param array $fullTextSearchWords
     * @param bool $replaceUmlauts Additionally add the words without umlauts
     * @param bool $isGenerated Flag for indicating if word really exists in content or has been generated for the search
     */
    private static function addWord(
        string $word,
        array &$fullTextSearchWords,
        bool $replaceUmlauts = true,
        bool $isGenerated = false
    ): void
    {
        // fix long words without spaces
        if (mb_strlen($word) > self::MAX_WORD_LENGTH) {
            $length = (int)floor(self::MAX_WORD_LENGTH / 2);
            // Find better position for splitting the word
            $splitChars = ['-', '.', '/'];
            foreach ($splitChars as $splitChar) {
                $splitPos = mb_strpos($word, $splitChar, (int) ($length * 0.75));
                if ($splitPos !== false && $splitPos < (int) ($length * 1.25)) {
                    $length = $splitPos;
                    break;
                }
            }
            $trimCharList = implode('', $splitChars);
            self::addWord(trim(mb_substr($word, 0, $length), $trimCharList), $fullTextSearchWords, $replaceUmlauts, $isGenerated);
            self::addWord(trim(mb_substr($word, $length - 1), $trimCharList), $fullTextSearchWords, $replaceUmlauts, $isGenerated);
        } else {
            if (!isset($fullTextSearchWords[$word])) {
                $fullTextSearchWords[$word] = [
                    'count' => 0,
                    'is_generated' => $isGenerated,
                ];
            }
            ++$fullTextSearchWords[$word]['count'];
            if ($replaceUmlauts) {
                // Word is already in lowercase; we don't need to replace uppercase umlauts
                $umlauts = [
                    'ö' => 'oe',
                    'ü' => 'ue',
                    'ä' => 'ae',
                    'ß' => 'ss',
                ];
                $search = array_keys($umlauts);
                $altWord = str_replace($search, $umlauts, $word);
                self::addWord($altWord, $fullTextSearchWords, false, true);
            }
        }
    }
}