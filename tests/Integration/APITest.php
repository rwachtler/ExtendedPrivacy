<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ExtendedPrivacy\tests\Integration;

use Piwik\Piwik;
use Piwik\Common;
use Piwik\Db;
use \Exception;
use Piwik\Plugins\ExtendedPrivacy\API;
use Piwik\Tests\Framework\Fixture;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

use Piwik\Tracker\Model as TrackerModel;

/**
 * @group ExtendedPrivacy
 * @group APITest
 * @group Plugins
 */
class APITest extends IntegrationTestCase
{

    /**
     * @var API
     */
    private $api;
    private $fakeVisit;
    private $fakeVisitorId;
    private $fakeVisitorIdString;

    public function setUp()
    {
        parent::setUp();

        $this->api = API::getInstance();
        $this->fakeVisitorId = hex2bin('1234567890');
        $this->fakeVisitorIdString = '1234567890000000';
        $this->fakeVisit = array(
            'idvisit' => 1028,
            'idsite' => 1,
            'idvisitor' => $this->fakeVisitorId,
            'visit_last_action_time' => '2018-02-24 09:29:18'
        );

        $trackerModel = new TrackerModel();
        $trackerModel->createVisit($this->fakeVisit);

        Fixture::createAccessInstance();
        Piwik::setUserHasSuperUserAccess();

        Fixture::createWebsite('2014-01-01 00:00:00');
        Fixture::createWebsite('2014-01-01 00:00:00');
    }

    public function test_getVisitorLogsCountByID_shouldReturnCountOfEntriesInsideVisitorLogTable() {
        $expectedResult = array(
            'HEX(idvisitor)' => $this->fakeVisitorIdString,
            'HEX(location_ip)' => NULL,
            'user_id' => NULL,
            'referer_keyword' => NULL,
            'referer_name' => NULL,
            'referer_url' => NULL,
            'location_browser_lang' => NULL,
            'config_browser_engine' => NULL,
            'config_browser_name' => NULL,
            'config_os' => NULL,
            'config_os_version' => NULL,
            'config_resolution' => NULL,
            'config_cookie' => NULL,
            'location_city' => NULL,
            'location_country' => NULL
        );

        $result = $this->api->getVisitorLogsByID($this->fakeVisitorIdString);
        $this->assertArrayHasKey('tableName', $result);
        $this->assertArrayHasKey('queryData', $result);
        $this->assertEquals($expectedResult, $result['queryData'][0]);
    }

    public function test_getVisitorLogsCountByID_shouldReturnAllOccurencesOfTheIdVisitor() {
        $expectedResult = array(
            (object) array(
                'tableName' => 'log_conversion',
                'quantity' => 0
            ),
            (object) array(
                'tableName' => 'log_conversion_item',
                'quantity' => 0
            ),
            (object) array(
                'tableName' => 'log_link_visit_action',
                'quantity' => 0
            ),
            (object) array(
                'tableName' => 'log_visit',
                'quantity' => 1
            )
        );
        $result = $this->api->getVisitorLogsCountByID($this->fakeVisitorIdString);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_deleteVisitorLogsByID_shouldDeleteAllDataWhereIdVisitorIsPresent() {
        $expectedResult = array(
            (object) array(
                'tableName' => 'log_conversion',
                'quantity' => 0
            ),
            (object) array(
                'tableName' => 'log_conversion_item',
                'quantity' => 0
            ),
            (object) array(
                'tableName' => 'log_link_visit_action',
                'quantity' => 0
            ),
            (object) array(
                'tableName' => 'log_visit',
                'quantity' => 0
            )
        );
        $this->api->deleteVisitorLogsByID($this->fakeVisitorIdString);
        $result = $this->api->getVisitorLogsCountByID($this->fakeVisitorIdString);
        $this->assertEquals($expectedResult, $result);
    }

    public function test_createOrUpdateTranslation_shouldReturnCustomValueAfterOverride() {
        $translationKey = 'YouMayOptOut';
        $expectedResult = array(
            'translation_key' => 'YouMayOptOut',
            'translation_value' => 'MyCustomTranslation!'
        );
        $table = Common::prefixTable('translation_overrides');
        $query = 'SELECT translation_key, translation_value FROM ' . $table .' WHERE translation_key = ?';
        $bind = array($translationKey);
        $this->api->createOrUpdateTranslation($translationKey, 'MyCustomTranslation!');
        $result = Db::fetchRow($query, $bind);
        $this->assertEquals($expectedResult, $result);
    }

    public function tearDown() {
        parent::tearDown();
    }
}
