<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ExtendedPrivacy;

use Piwik\Common;
use Piwik\Config as PiwikConfig;
use Piwik\Plugins\PrivacyManager\Config;
use Piwik\Plugins\PrivacyManager\IPAnonymizer;
use Piwik\Plugins\LanguagesManager\LanguagesManager;
use Piwik\Piwik;
use Piwik\UrlHelper;
use Piwik\View;

/**
 * A controller lets you for example create a page that can be added to a menu. For more information read our guide
 * http://developer.piwik.org/guides/mvc-in-piwik or have a look at the our API references for controller and view:
 * http://developer.piwik.org/api-reference/Piwik/Plugin/Controller and
 * http://developer.piwik.org/api-reference/Piwik/View
 */
class Controller extends \Piwik\Plugin\Controller
{
    /** @var OptInManager */
    private $optInManager;
    /** @var DOMHelper */
    private $domHelper;

    public function __construct(OptInManager $optInManager, DOMHelper $domHelper) {
        $this->optInManager = $optInManager;
        $this->domHelper = $domHelper;
        parent::__construct();
    }

    public function extendedPrivacySettings() {
        Piwik::checkUserHasSomeAdminAccess();
        if (Piwik::hasUserSuperUserAccess()) {
            $trackingInfo = $this->domHelper->getTrackingInfoIframe();
            $anonymizeIPInfo = $this->getAnonymizeIPInfo();
            $defaultOptOutTranslation =
                Piwik::translate('CoreAdminHome_YouMayOptOut') . '<br/>' .
                Piwik::translate('CoreAdminHome_YouMayOptOutBis') . '<br/>' .
                Piwik::translate('CoreAdminHome_YouAreOptedIn') . '<br />' .
                Piwik::translate('CoreAdminHome_ClickHereToOptOut');
            $viewData = array(
                'transparencyType' => isset($trackingInfo['type']) ? $trackingInfo['type'] : '',
                'anonymizeIPInUse' => $anonymizeIPInfo['enabled'],
                'anonymizeIPMaskLength' => $anonymizeIPInfo['maskLength'],
                'anonymizeIPForAnonymousVisitEnrichment' => $anonymizeIPInfo['useAnonymizedIpForVisitEnrichment'],
                'anonymizeIPExamplePreview' => $anonymizeIPInfo['example'],
                'language' => LanguagesManager::getLanguageCodeForCurrentUser()
            );
        }
        return $this->renderTemplate('index', $viewData);
    }

    /**
     * Shows the Opt-In checkbox.
     */
    public function optIn()
    {
        return $this->optInManager->getOptInView()->render();
    }

    protected function getAnonymizeIPInfo() {
        Piwik::checkUserHasSuperUserAccess();
        $anonymizeIP = array();
        $demoIP = '203.0.113.195';

        $privacyConfig = new Config();
        $anonymizeIP["enabled"] = IPAnonymizer::isActive();
        $anonymizeIP["maskLength"] = $privacyConfig->ipAddressMaskLength;
        $anonymizeIP["useAnonymizedIpForVisitEnrichment"] = $privacyConfig->useAnonymizedIpForVisitEnrichment;
        if (!$anonymizeIP["useAnonymizedIpForVisitEnrichment"]) {
            $anonymizeIP["useAnonymizedIpForVisitEnrichment"] = '0';
        }
        $splittedIP = explode('.', $demoIP);
        for ($i = $privacyConfig->ipAddressMaskLength; $i > 0; $i--) {
            $splittedIP[count($splittedIP)-$i] = 'x';
        }
        $anonymizeIP["example"] = implode('.', $splittedIP);

        return $anonymizeIP;
    }
}
