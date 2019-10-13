<?php
declare(strict_types=1);

namespace FriendsOfTypo3\TtAddress\Tests\Unit\Domain\Model;

/**
 * This file is part of the "uri2link" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use GeorgRinger\Uri2Link\Hooks\DataHandlerHook;
use GeorgRinger\Uri2Link\Service\UrlParser;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\TestingFramework\Core\BaseTestCase;

class DataHanderHookTest extends BaseTestCase
{

    protected function setUp(): void
    {
        $GLOBALS['TCA']['fakeTable1']['columns']['field_1']['config']['renderType'] = 'inputLink';
        $GLOBALS['TCA']['fakeTable1']['columns']['field_2']['config']['type'] = 'somethingElse';
        $GLOBALS['TCA']['fakeTable2']['columns']['field_3']['config']['renderType'] = 'inputLink';

        parent::setUp();
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TCA']['fakeTable1'], $GLOBALS['TCA']['fakeTable2']);
        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider fieldProcessingWorksDataProvider
     * @param string $fieldName
     * @param mixed $fieldValue
     * @param bool $expected
     */
    public function fieldProcessingWorks(string $tableName, string $fieldName, $fieldValue, bool $expected): void
    {
        $subject = $this->getAccessibleMock(DataHandlerHook::class, ['dummy'], [], '', false);
        $this->assertEquals($expected, $subject->_call('fieldShouldBeProcessed', $tableName, $fieldName, $fieldValue));
    }

    public function fieldProcessingWorksDataProvider(): array
    {
        return [
            'link field' => ['fakeTable1', 'field_1', 'http://domain.tld', true],
            'link field with https' => ['fakeTable1', 'field_1', 'https://domain.tld', true],
            'link field with / at beginning' => ['fakeTable1', 'field_1', '/', true],
            'link field with email link' => ['fakeTable1', 'field_1', 'fo@bar.com', false],
            'link field with empty link' => ['fakeTable1', 'field_1', '', false],
            'link field with different type' => ['fakeTable1', 'field_1', 123, false],
            'none existing table' => ['fakeTable3', 'field_1', 123, false],
            'none existing field' => ['fakeTable1', 'field_9', 'http://domain.tld', false],
            'wrong type' => ['fakeTable1', 'field_2', 'http://domain.tld', false],
        ];
    }

    /**
     * @test
     */
    public function urlParserIsCalled(): void
    {
        $mockedUrlParser = $this->getAccessibleMock(UrlParser::class, ['parse'], [], '', false);
        $mockedUrlParser->expects($this->once())->method('parse');
        $mockedDataHandler = $this->getAccessibleMock(DataHandler::class, ['dummy'], [], '', false);

        $subject = $this->getAccessibleMock(DataHandlerHook::class, ['dummy'], [], '', false);
        $subject->_set('urlParser', $mockedUrlParser);

        $fields = [
            'field_1' => 'http://domain.tld/fo'
        ];
        $subject->processDatamap_postProcessFieldArray('update', 'fakeTable1', 123, $fields, $mockedDataHandler);
    }

}
