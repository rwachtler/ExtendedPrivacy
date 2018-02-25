<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ExtendedPrivacy;

use Piwik\Common;
use Piwik\Plugins\LanguagesManager\LanguagesManager;
use Piwik\Piwik;
use Piwik\Site;
use Piwik\Url;
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
    public function extendedPrivacySettings() {
        Piwik::checkUserHasSomeAdminAccess();
        if (Piwik::hasUserSuperUserAccess()) {
            $trackingInfo = $this->getTrackingInfoIframe();
            $viewData = array(
                'transparencyType' => isset($trackingInfo['type']) ? $trackingInfo['type'] : '',
                'transparencyIframeContent' => isset($trackingInfo['content']) ? $trackingInfo['content'] : ''
            );
        }
        return $this->renderTemplate('index', $viewData);
    }

    /**
     * Retrieves information from tracking iframes
     */
    protected function getTrackingInfoIframe() {
        $UrlInstance = new Url();
        $query = $UrlInstance->getArrayFromCurrentQueryString();
        $SiteInstance = new Site($query['idSite']);
        $mainUrl = $SiteInstance->getMainUrl();
        $possibleSubpages = array(
            '',
            Piwik::translate('ExtendedPrivacy_UrlPrivacy'),
            Piwik::translate('ExtendedPrivacy_UrlPrivacyAlt'),
            Piwik::translate('ExtendedPrivacy_UrlTerms'),
            Piwik::translate('ExtendedPrivacy_UrlLegal')
        );

        $iframesArr = array();
        libxml_use_internal_errors(true);
        foreach ($possibleSubpages as $subpage) {
            $DOMInstance = new \DOMDocument();
            $requestUrl = $mainUrl . '/' . $subpage;
            $urlValidity = get_headers($requestUrl , 1);
            if (strpos($urlValidity[0], '200 OK')) {
                $DOMInstance->loadHTMLFile($requestUrl);
                $iframeElementsForSubpage = $DOMInstance->getElementsByTagName('iframe');
                if ($iframeElementsForSubpage->length > 0) {
                    $iframesArr[] = $iframeElementsForSubpage;
                }
            }
        }
        foreach ($iframesArr as $nodeList) {
            foreach ($nodeList as $iframeNode) {
                $iframeTargetUrl = $iframeNode->attributes[1]->value;
                if (strpos($iframeTargetUrl, 'module=CoreAdminHome&action=optOut')) {
                    $DOMInstance->loadHTMLFile($iframeTargetUrl);
                    $content = $DOMInstance->getElementsByTagName('body');
                    return array(
                        'type' => 'default',
                        'content' => $content[0]->nodeValue
                    );
                }
            }
        }
        return array();
    }
}
