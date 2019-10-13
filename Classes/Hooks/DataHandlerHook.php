<?php
declare(strict_types=1);

namespace GeorgRinger\Uri2Links\Hooks;

use GeorgRinger\Uri2Links\Service\UrlParser;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

class DataHandlerHook
{

    /** @var UrlParser */
    protected $urlParser;

    public function __construct()
    {
        $this->urlParser = GeneralUtility::makeInstance(UrlParser::class);
    }

    /**
     * Fill path_segment/slug field with title
     *
     * @param string $status
     * @param string $table
     * @param string|int $id
     * @param array $fieldArray
     * @param DataHandler $parentObject
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, array &$fieldArray, DataHandler $parentObject): void
    {
        foreach ($fieldArray as $fieldName => $fieldValue) {
            if ($this->fieldShouldBeProcessed($table, $fieldName, $fieldValue)) {
                try {
                    $fieldArray[$fieldName] = $this->urlParser->parse($fieldValue);
                } catch (\Exception $exception) {
                    // do nothing
                }
            }
        }
    }

    protected function fieldShouldBeProcessed(string $tableName, string $fieldName, $fieldValue): bool
    {
        if (empty($fieldValue) || !is_string($fieldValue)) {
            return false;
        }

        if (!isset($GLOBALS['TCA'][$tableName])) {
            return false;
        }
        if (!isset($GLOBALS['TCA'][$tableName]['columns'][$fieldName])) {
            return false;
        }

        if ($GLOBALS['TCA'][$tableName]['columns'][$fieldName]['config']['renderType'] === 'inputLink'
            && (StringUtility::beginsWith($fieldValue, 'http') || StringUtility::beginsWith($fieldValue, '/'))
        ) {
            return true;
        }

        return false;
    }

}
