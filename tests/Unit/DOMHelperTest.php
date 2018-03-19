<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\ExtendedPrivacy\tests\Unit;

use Piwik\Plugins\ExtendedPrivacy\DOMHelper;

/**
 * @group ExtendedPrivacy
 * @group DOMHelperTest
 * @group Plugins
 */
class DOMHelperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp() {
        $this->domHelper = new DOMHelper();
    }

    public function testGetAttributes() {
        $html = '<iframe src="http://mysource.com" id="myIframe" class="iframe--custom"></iframe>';
        $htmlWithoutAttributes = '<iframe></iframe>';
        $DOMInstance = new \DOMDocument();
        $DOMInstance->loadHTML($html);
        $iframe = $DOMInstance->getElementsByTagName('iframe')[0];

        $class = $this->domHelper->getAttribute('class', $iframe->attributes);
        $id = $this->domHelper->getAttribute('id', $iframe->attributes);
        $src = $this->domHelper->getAttribute('src', $iframe->attributes);
        $name = $this->domHelper->getAttribute('name', $iframe->attributes);

        $this->assertEquals($class, 'iframe--custom');
        $this->assertEquals($id, 'myIframe');
        $this->assertEquals($src, 'http://mysource.com');
        $this->assertEquals($name, '');

        $DOMInstance->loadHTML($htmlWithoutAttributes);
        $iframeWithoutAttributes = $DOMInstance->getElementsByTagName('iframe')[0];

        $class = $this->domHelper->getAttribute('class', $iframeWithoutAttributes->attributes);
        $this->assertEquals($class, '');
    }

}
