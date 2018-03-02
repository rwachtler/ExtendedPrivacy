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