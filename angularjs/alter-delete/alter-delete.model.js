/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
(function () {
    angular.module('piwikApp.service').factory('AlterDeleteModel', AlterDeleteModel);

    AlterDeleteModel.$inject = ['piwik', 'piwikApi'];


    function AlterDeleteModel(piwik, piwikApi) {
        var model = {
            getDataCountForVisitorID
        };

        return model;

        function getVisitorLogsByID(id) {
            return piwikApi.fetch({
                id,
                module: 'API',
                method: 'ExtendedPrivacy.getVisitorLogsByID'
            });
        }

        function getVisitorLogsCountByID(id) {
            return piwikApi.fetch({
                id,
                module: 'API',
                method: 'ExtendedPrivacy.getVisitorLogsCountByID'
            });
        }

        function getDataCountForVisitorID(id) {
            return getVisitorLogsCountByID(id);
        }
    }
})();