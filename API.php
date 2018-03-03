<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ExtendedPrivacy;

use Piwik\DataTable;
use Piwik\DataTable\Row;
use Piwik\Piwik;

/**
 * API for plugin ExtendedPrivacy
 *
 * @method static \Piwik\Plugins\ExtendedPrivacy\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    /**
     * @param  int $id
     *
     * @return array
     */
    public function getVisitorLogsByID($id) {
        Piwik::checkUserHasSuperUserAccess();

        return $this->getModel()->getVisitorLogsByID($id);
    }

    public function getVisitorLogsCountByID($id) {
        Piwik::checkUserHasSuperUserAccess();
        $data = $this->getModel()->getLogsForVisitorID($id);
        return array(
            (object) array(
                'tableName' => 'log_conversion',
                'quantity' => (int)$data[0]['log_conversion']
            ),
            (object) array(
                'tableName' => 'log_conversion_item',
                'quantity' => (int)$data[0]['log_conversion_item']
            ),
            (object) array(
                'tableName' => 'log_link_visit_action',
                'quantity' => (int)$data[0]['log_link_visit_action']
            ),
            (object) array(
                'tableName' => 'log_visit',
                'quantity' => (int)$data[0]['log_visit']
            )
        );
    }

    private function getModel() {
        return new Model();
    }
}
