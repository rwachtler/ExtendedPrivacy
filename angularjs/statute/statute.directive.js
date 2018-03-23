/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

/**
 * Usage:
 * <div piwik-statute>
 */
(function () {
    angular.module('piwikApp').directive('extendedPrivacyStatute', extendedPrivacyStatute);

    extendedPrivacyStatute.$inject = ['piwik', 'StatuteModel'];

    function extendedPrivacyStatute(piwik, StatuteModel) {
        var defaults = {};

        return {
            restrict: 'A',
            scope: {
                source: '@'
            },
            templateUrl: 'plugins/ExtendedPrivacy/angularjs/statute/statute.directive.html?cb=' + piwik.cacheBuster,
            controller: 'StatuteController',
            controllerAs: 'statute',
            compile: function (element, attrs) {

                for (var index in defaults) {
                    if (defaults.hasOwnProperty(index) && attrs[index] === undefined) {
                        attrs[index] = defaults[index];
                    }
                }

                return function (scope, element, attrs) {
                    element.bind('click', () => {
                        const { source } = scope;
                        StatuteModel.getStatute(source)
                            .then(url => window.open(url, '_blank'));
                    })
                };
            }
        };
    }
})();