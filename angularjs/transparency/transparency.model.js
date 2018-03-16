/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
(function () {
    angular.module('piwikApp.service').factory('TransparencyModel', TransparencyModel);

    TransparencyModel.$inject = ['piwik', 'piwikApi'];


    function TransparencyModel(piwik, piwikApi) {
        var model = {
            createOrUpdateTranslation
        };

        return model;

        function createOrUpdateTranslation(translationKey, translationValue) {
            return piwikApi.post({ module: 'API', method: 'ExtendedPrivacy.createOrUpdateTranslation' }, {
                translationKey,
                translationValue
            });
        }
    }
})();