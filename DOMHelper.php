<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ExtendedPrivacy;

use Piwik\Piwik;
use Piwik\Site;
use Piwik\Url;

class DOMHelper {
    /**
     * Retrieves information from tracking iframes
     */
    public function getTrackingInfoIframe() {
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
                $iframeTargetUrl = $this->getAttribute('src', $iframeNode->attributes);
                if (strpos($iframeTargetUrl, 'module=CoreAdminHome&action=optOut')) {
                    $DOMInstance->loadHTMLFile($iframeTargetUrl);
                    $content = $DOMInstance->getElementsByTagName('body');
                    return array(
                        'type' => 'default',
                        'content' => $content[0]->nodeValue
                    );
                } else if (strpos($iframeTargetUrl, 'module=ExtendedPrivacy&action=optIn')) {
                    $DOMInstance->loadHTMLFile($iframeTargetUrl);
                    $content = $DOMInstance->getElementsByTagName('body');
                    return array(
                        'type' => 'optIn',
                        'content' => $content[0]->nodeValue
                    );
                }
            }
        }
        return array();
    }

    /**
     * Returns attribute value for given attribute name
     *
     * $name - Name of the attribute (String)
     * $attributes - Node attributes (DOMNamedNodeMap Object)
     */
    public function getAttribute($name, $attributes) {
        foreach ($attributes as $attribute) {
            if ($attribute->name===$name) {
                return $attribute->value;
            }
        }
    }
}
