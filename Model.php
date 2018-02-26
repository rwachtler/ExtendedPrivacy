<?php

namespace Piwik\Plugins\ExtendedPrivacy;

use Piwik\Common;
use Piwik\Db;
use Piwik\DbHelper;

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
        $bind = array($visitorID);
        $query = 'SELECT * FROM ' . $this->table . ' WHERE idvisitor = ?';

        return $this->getDb()->fetchAll($query, $bind);
    }

    private function getDb() {
        return Db::get();
    }
}