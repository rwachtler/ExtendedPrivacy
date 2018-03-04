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

        // EXAMPLE: $translations['CoreAdminHome']['YouMayOptOut'] = 'my custom text';

        return $translations;
    }
}
