<?php

namespace Piwik\Plugins\ExtendedPrivacy;

use Piwik\Common;
use Piwik\Db;
use Piwik\DbHelper;
use Piwik\Container\StaticContainer;

class Model {

    private static $rawPrefix = 'log_visit';
    private $table;

    public function __construct() {
        $this->table = Common::prefixTable(self::$rawPrefix);
    }

    /**
     * Returns all visitor logs for the given Visitor-ID
     */
    public function getVisitorLogsByID($visitorID) {
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
        $query = 'SELECT ' . $columns . ' FROM ' . $this->table . ' WHERE HEX(idvisitor) = ?';

        return $this->getDb()->fetchAll($query, $bind);
    }

    private function getDb() {
        return Db::get();
    }
}