<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ExtendedPrivacy;

class ExtendedPrivacy extends \Piwik\Plugin
{
    public function registerEvents() {
        return array(
            'AssetManager.getJavaScriptFiles' => 'getJavaScriptFiles',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys'
        );
    }

    public function getJavaScriptFiles(&$files) {
        $files[] = "plugins/ExtendedPrivacy/angularjs/alter-delete/alter-delete.controller.js";
        $files[] = "plugins/ExtendedPrivacy/angularjs/alter-delete/alter-delete.model.js";
    }

    public function getClientSideTranslationKeys(&$translationKeys) {
        $translationKeys[] = 'ExtendedPrivacy_GenericError';
        $translationKeys[] = 'ExtendedPrivacy_GenericSuccess';
    }

}
