/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

(function () {
    angular.module('piwikApp').controller('TransparencyController', TransparencyController);

    TransparencyController.$inject = ['TransparencyModel', 'piwikApi'];

    function TransparencyController(TransparencyModel, piwikApi) {
        // remember to keep controller very simple. Create a service/factory (model) if needed
        const self = this;
        const UI = require('piwik/UI');
        this.showDetails = false;
        this.performedRequest = false;
        this.YouMayOptOut = initTranslation('YouMayOptOut');
        this.ClickHereToOptOut = initTranslation('ClickHereToOptOut');
        this.YouMayOptOutBis = initTranslation('YouMayOptOutBis');
        this.YouAreOptedIn = initTranslation('YouAreOptedIn');

        this.save = function (key) {
            // TODO: implement
        }

        function initTranslation(key) {
            return _pk_translate(`CoreAdminHome_${key}`);
        }
    }
})();
