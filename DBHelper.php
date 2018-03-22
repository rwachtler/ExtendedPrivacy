<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ExtendedPrivacy;

use Piwik\Piwik;
use Piwik\Config;
use Piwik\Db;
use \Exception;

class DBHelper {
    /**
     * Creates a new table within piwik_tests
     *
     * @param string $tableName
     */
    public function createCustomTestingTable($tableName) {
        $config = Config::getInstance();
        $dbName = $config->database['dbname'];
        $tablePrefix = $config->database['tables_prefix'];
        $table = $dbName . '.' . $tablePrefix . $tableName;

        try {
            $sql = "CREATE TABLE " . $table . " (
                        translation_key VARCHAR( 30 ) NOT NULL ,
                        translation_value VARCHAR( 500 ) NOT NULL ,
                        PRIMARY KEY ( translation_key )
                    )  DEFAULT CHARSET=utf8 ";
            Db::exec($sql);
        } catch (Exception $e) {
            // ignore error if table already exists (1050 code is for 'table already exists')
            if (!Db::get()->isErrNo($e, '1050')) {
                throw $e;
            }
        }
    }
}