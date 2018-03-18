<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Plugins\ExtendedPrivacy;

use Piwik\Common;
use Piwik\Cookie;
use Piwik\Nonce;
use Piwik\Plugins\LanguagesManager\API as APILanguagesManager;
use Piwik\Plugins\LanguagesManager\LanguagesManager;
use Piwik\Plugins\PrivacyManager\DoNotTrackHeaderChecker;
use Piwik\Tracker\IgnoreCookie;
use Piwik\Url;
use Piwik\View;

class OptInManager
{
    /** @var DoNotTrackHeaderChecker */
    private $doNotTrackHeaderChecker;

    /** @var array */
    private $javascripts;

    /** @var array */
    private $stylesheets;

    /** @var string */
    private $title;

    /** @var View|null */
    private $view;

    /** @var array */
    private $queryParameters = array();

    private $customTrackCookie;

    private $customDoNotTrackCookie;

    /**
     * @param DoNotTrackHeaderChecker $doNotTrackHeaderChecker
     */
    public function __construct(DoNotTrackHeaderChecker $doNotTrackHeaderChecker = null)
    {
        $this->doNotTrackHeaderChecker = $doNotTrackHeaderChecker ?: new DoNotTrackHeaderChecker();

        $this->javascripts = array(
            'inline' => array(),
            'external' => array(),
        );

        $this->stylesheets = array(
            'inline' => array(),
            'external' => array(),
        );
    }

    /**
     * Add a javascript file|code into the OptOut View
     * Note: This method will not escape the inline javascript code!
     *
     * @param string $javascript
     * @param bool $inline
     */
    public function addJavascript($javascript, $inline = true)
    {
        $type = $inline ? 'inline' : 'external';
        $this->javascripts[$type][] = $javascript;
    }

    /**
     * @return array
     */
    public function getJavascripts()
    {
        return $this->javascripts;
    }

    /**
     * Add a stylesheet file|code into the OptOut View
     * Note: This method will not escape the inline css code!
     *
     * @param string $stylesheet Escaped stylesheet
     * @param bool $inline
     */
    public function addStylesheet($stylesheet, $inline = true)
    {
        $type = $inline ? 'inline' : 'external';
        $this->stylesheets[$type][] = $stylesheet;
    }

    /**
     * @return array
     */
    public function getStylesheets()
    {
        return $this->stylesheets;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $key
     * @param string $value
     * @param bool $override
     *
     * @return bool
     */
    public function addQueryParameter($key, $value, $override = true)
    {
        if (!isset($this->queryParameters[$key]) || true === $override) {
            $this->queryParameters[$key] = $value;
            return true;
        }

        return false;
    }

    /**
     * @param array $items
     * @param bool|true $override
     */
    public function addQueryParameters(array $items, $override = true)
    {
        foreach ($items as $key => $value) {
            $this->addQueryParameter($key, $value, $override);
        }
    }

    /**
     * @param $key
     */
    public function removeQueryParameter($key)
    {
        unset($this->queryParameters[$key]);
    }

    /**
     * @return array
     */
    public function getQueryParameters()
    {
        return $this->queryParameters;
    }

    /**
     * @return View
     * @throws \Exception
     */
    public function getOptInView()
    {
        if ($this->view) {
            return $this->view;
        }

        $this->customTrackCookie = new Cookie('piwik_track', null, '/');
        $this->customDoNotTrackCookie = new Cookie('piwik_do-not-track', null, '/');
        $foundCustomTrackCookie = $this->customTrackCookie->isCookieFound();
        $foundCustomDoNotTrackCookie = $this->customDoNotTrackCookie->isCookieFound();
        $foundIgnoreCookie = IgnoreCookie::isIgnoreCookieFound();
        $dntFound = $this->getDoNotTrackHeaderChecker()->isDoNotTrackFound();

        $trackVisits = $foundIgnoreCookie ? false : true;

        if (!$foundIgnoreCookie && !$foundCustomDoNotTrackCookie && !$foundCustomTrackCookie) {
            IgnoreCookie::setIgnoreCookie();
            $this->setCustomDoNotTrackCookie();
            $trackVisits = false;
        } else if (!$foundIgnoreCookie && $foundCustomDoNotTrackCookie) {
            $this->customDoNotTrackCookie->delete();
            $this->setCustomTrackCookie();
        } else if ($foundIgnoreCookie && $foundCustomTrackCookie) {
            $this->customTrackCookie->delete();
            $this->setCustomDoNotTrackCookie();
        }

        $setCookieInNewWindow = Common::getRequestVar('setCookieInNewWindow', false, 'int');
        if ($setCookieInNewWindow) {
            $reloadUrl = Url::getCurrentQueryStringWithParametersModified(array(
                'showConfirmOnly' => 1,
                'setCookieInNewWindow' => 0,
            ));
        } else {
            $reloadUrl = false;
            $nonce = Common::getRequestVar('nonce', false);
            if ($nonce !== false && Nonce::verifyNonce('Piwik_OptIn', $nonce)) {
                Nonce::discardNonce('Piwik_OptIn');
                IgnoreCookie::setIgnoreCookie();
                $trackVisits = !$trackVisits;
            }
        }

        $language = Common::getRequestVar('language', '');
        $lang = APILanguagesManager::getInstance()->isLanguageAvailable($language)
            ? $language
            : LanguagesManager::getLanguageCodeForCurrentUser();

        $this->addQueryParameters(array(
            'module' => 'ExtendedPrivacy',
            'action' => 'optIn',
            'language' => $lang,
            'setCookieInNewWindow' => 1
        ), false);

        $this->view = new View("@ExtendedPrivacy/optIn");
        $this->view->setXFrameOptions('allow');
        $this->view->dntFound = $dntFound;
        $this->view->trackVisits = $trackVisits;
        $this->view->nonce = Nonce::getNonce('Piwik_OptIn', 3600);
        $this->view->language = $lang;
        $this->view->showConfirmOnly = Common::getRequestVar('showConfirmOnly', false, 'int');
        $this->view->reloadUrl = $reloadUrl;
        $this->view->javascripts = $this->getJavascripts();
        $this->view->stylesheets = $this->getStylesheets();
        $this->view->title = $this->getTitle();
        $this->view->queryParameters = $this->getQueryParameters();

        return $this->view;
    }

    private function setCustomTrackCookie() {
        $this->customTrackCookie->set('piwik_track', 'true');
        $this->customTrackCookie->save();
    }

    private function setCustomDoNotTrackCookie() {
        $this->customDoNotTrackCookie->set('piwik_do-not-track', 'true');
        $this->customDoNotTrackCookie->save();
    }

    /**
     * @return DoNotTrackHeaderChecker
     */
    protected function getDoNotTrackHeaderChecker()
    {
        return $this->doNotTrackHeaderChecker;
    }
}
