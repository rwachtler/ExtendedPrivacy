<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ExtendedPrivacy;

use Piwik\Option;

/**
 * Is used to override the default Opt-Out messages
 */
class CustomTranslationLoader extends \Piwik\Translation\Loader\JsonFileLoader
{
    public function load($language, array $directories)
    {
        $translations = parent::load($language, $directories);

        $YouMayOptOut = $this->getModel()->fetchTranslation('YouMayOptOut');
        $ClickHereToOptOut = $this->getModel()->fetchTranslation('ClickHereToOptOut');
        $YouMayOptOutBis = $this->getModel()->fetchTranslation('YouMayOptOutBis');
        $YouAreOptedIn = $this->getModel()->fetchTranslation('YouAreOptedIn');
        $OptedOut = $this->getModel()->fetchTranslation('OptedOut');
        $OptOutInfo = $this->getModel()->fetchTranslation('OptOutInfo');

        if ($YouMayOptOut) {
            $translations['CoreAdminHome']['YouMayOptOut'] = $YouMayOptOut;
        }
        if ($ClickHereToOptOut) {
            $translations['CoreAdminHome']['ClickHereToOptOut'] = $ClickHereToOptOut;
        }
        if ($YouMayOptOutBis) {
            $translations['CoreAdminHome']['YouMayOptOutBis'] = $YouMayOptOutBis;
        }
        if ($YouAreOptedIn) {
            $translations['CoreAdminHome']['YouAreOptedIn'] = $YouAreOptedIn;
        }
        if ($OptedOut) {
            $translations['ExtendedPrivacy']['OptedOut'] = $OptedOut;
        }
        if ($OptOutInfo) {
            $translations['ExtendedPrivacy']['OptOutInfo'] = $OptOutInfo;
        }

        return $translations;
    }

    private function getModel() {
        return new Model();
    }
}
