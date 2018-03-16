<?php

namespace Piwik\Plugins\ExtendedPrivacy;

use Piwik\Common;
use Piwik\Db;
use Piwik\DbHelper;
use Piwik\Container\StaticContainer;

class Model {
    /**
     * Returns all visitor logs for the given Visitor-ID
     */
    public function getVisitorLogsByID($visitorID) {
        $rawPrefix = 'log_visit';
        $table = Common::prefixTable($rawPrefix);
        $columns = implode(', ', array(
            'HEX(idvisitor)',
            'HEX(location_ip)',
            'user_id',
            'referer_keyword',
            'referer_name',
            'referer_url',
            'location_browser_lang',
            'config_browser_engine',
            'config_browser_name',
            'config_os',
            'config_os_version',
            'config_resolution',
            'config_cookie',
            'location_city',
            'location_country'
        ));
        $bind = array($visitorID);
        $query = 'SELECT ' . $columns . ' FROM ' . $table . ' WHERE HEX(idvisitor) = ?';
        $queryResult = $this->getDb()->fetchAll($query, $bind);

        return $this->dataWithTableName($rawPrefix, $queryResult);
    }

    /**
     * Returns the amount of entries inside the `log_visit` table
     */
    public function getVisitorLogsCountByID($visitorID) {
        $rawPrefix = 'log_visit';
        $table = Common::prefixTable($rawPrefix);
        $bind = array($visitorID);
        $query = 'SELECT COUNT(HEX(idvisitor)) AS entries' . ' FROM ' . $table . ' WHERE HEX(idvisitor) = ?';
        $queryResult = $this->getDb()->fetchOne($query, $bind);

        return $this->dataWithTableName($rawPrefix, $queryResult);
    }

    /**
     * Returns the amount of database entries where a visitorID is present
     * Following tables will be included
     * log_conversion
     * log_conversion_item
     * log_link_visit_action
     * log_visit
     *
     */
    public function getLogsForVisitorID($visitorID) {
        $rawPrefixConversion = 'log_conversion';
        $rawPrefixConversionItem = 'log_conversion_item';
        $rawPrefixLinkVisitAction = 'log_link_visit_action';
        $rawPrefixVisit = 'log_visit';

        $tableConversion = Common::prefixTable($rawPrefixConversion);
        $tableConversionItem = Common::prefixTable($rawPrefixConversionItem);
        $tableLinkVisitAction = Common::prefixTable($rawPrefixLinkVisitAction);
        $tableVisit = Common::prefixTable($rawPrefixVisit);

        $bind = array($visitorID, $visitorID, $visitorID, $visitorID);
        $query = 'SELECT ' .
                    '(SELECT COUNT(HEX(idvisitor)) AS entries FROM ' . $tableConversion . ' WHERE HEX(idvisitor) = ?) as ' . $rawPrefixConversion . ', ' .
                    '(SELECT COUNT(HEX(idvisitor)) AS entries FROM ' . $tableConversionItem . ' WHERE HEX(idvisitor) = ?) as ' . $rawPrefixConversionItem . ', ' .
                    '(SELECT COUNT(HEX(idvisitor)) AS entries FROM ' . $tableLinkVisitAction . ' WHERE HEX(idvisitor) = ?) as ' . $rawPrefixLinkVisitAction . ', ' .
                    '(SELECT COUNT(HEX(idvisitor)) AS entries FROM ' . $tableVisit . ' WHERE HEX(idvisitor) = ?) as ' . $rawPrefixVisit . '';

        $queryResult = $this->getDb()->fetchAll($query, $bind);

        return $queryResult;
    }

    /**
     * Deletes all data for a given visitor-id
     * Following tables will be included
     * log_conversion
     * log_conversion_item
     * log_link_visit_action
     * log_visit
     */
    public function deleteVisitorLogsByID($id) {
        $tables = array('log_conversion', 'log_conversion_item', 'log_link_visit_action', 'log_visit');
        $deletionResult = array();
        foreach ($tables as &$tableName) {
            $deletionResult[$tableName] = Db::deleteAllRows(
                Common::prefixTable($tableName),
                'WHERE HEX(idvisitor) = ?',
                'HEX(idvisitor) ASC',
                100000,
                array($id)
            );
        }
        unset($tableName);

        return $deletionResult;
    }

    /**
     * Populates custom overrides into the translation_overrides table
     * $translationKey - original Matomo translation key
     * $translationValue - desired translation value
     */
    public function createOrUpdateTranslation($translationKey, $translationValue) {
        $rawPrefix = 'translation_overrides';
        $table = Common::prefixTable($rawPrefix);
        $bind = array($translationKey, $translationValue, $translationValue);
        $query = sprintf(
            'INSERT INTO %s (translation_key, translation_value) VALUES (?,?) ON DUPLICATE KEY UPDATE translation_value=?',
             $table
        );

        Db::query($query, $bind);
    }

    private function dataWithTableName($table, $queryResult) {
        return array(
            'tableName' => $table,
            'queryData' => $queryResult
        );
    }

    private function getDb() {
        return Db::get();
    }
}