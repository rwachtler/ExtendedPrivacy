<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ExtendedPrivacy;
use Piwik\Db;
use Piwik\Common;
use \Exception;

class ExtendedPrivacy extends \Piwik\Plugin
{
    public function install() {
        try {
            $sql = "CREATE TABLE " . Common::prefixTable('translation_overrides') . " (
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

    public function uninstall() {
        Db::dropTables(Common::prefixTable('translation_overrides'));
    }

    public function registerEvents() {
        return array(
            'AssetManager.getStylesheetFiles' => 'getStylesheetFiles',
            'AssetManager.getJavaScriptFiles' => 'getJavaScriptFiles',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys'
        );
    }

    public function getJavaScriptFiles(&$files) {
        $files[] = "plugins/ExtendedPrivacy/angularjs/alter-delete/alter-delete.controller.js";
        $files[] = "plugins/ExtendedPrivacy/angularjs/alter-delete/alter-delete.model.js";
        $files[] = "plugins/ExtendedPrivacy/angularjs/transparency/transparency.controller.js";
        $files[] = "plugins/ExtendedPrivacy/angularjs/transparency/transparency.model.js";
        $files[] = "plugins/ExtendedPrivacy/angularjs/statute/statute.model.js";
        $files[] = "plugins/ExtendedPrivacy/angularjs/statute/statute.controller.js";
        $files[] = "plugins/ExtendedPrivacy/angularjs/statute/statute.directive.js";
    }

    public function getStylesheetFiles(&$files) {
        $files[] = "plugins/ExtendedPrivacy/stylesheets/main.less";
        $files[] = "plugins/ExtendedPrivacy/stylesheets/utils.less";
        $files[] = "plugins/ExtendedPrivacy/angularjs/statute/statute.directive.less";
    }

    public function getClientSideTranslationKeys(&$translationKeys) {
        $translationKeys[] = 'ExtendedPrivacy_DataPrivacyAct';
        $translationKeys[] = 'ExtendedPrivacy_GenericError';
        $translationKeys[] = 'ExtendedPrivacy_GenericSuccess';
        $translationKeys[] = 'ExtendedPrivacy_OptedOut';
        $translationKeys[] = 'ExtendedPrivacy_OptOutInfo';
        $translationKeys[] = 'ExtendedPrivacy_SectionType';
        $translationKeys[] = 'ExtendedPrivacy_StatuteSource';
        $translationKeys[] = 'CoreAdminHome_YouMayOptOut';
        $translationKeys[] = 'CoreAdminHome_YouAreOptedIn';
        $translationKeys[] = 'CoreAdminHome_YouAreOptedOut';
        $translationKeys[] = 'CoreAdminHome_YouMayOptOutBis';
        $translationKeys[] = 'CoreAdminHome_ClickHereToOptOut';
        $translationKeys[] = 'CoreAdminHome_ClickHereToOptIn';
        $translationKeys[] = 'CoreAdminHome_OptOutComplete';
        $translationKeys[] = 'CoreAdminHome_OptOutCompleteBis';
    }

}
