/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

/**
 * Usage:
 * <piwik-alter-delete>
 */
(function () {
    angular.module('piwikApp').controller('AlterDeleteController', AlterDeleteController);

    AlterDeleteController.$inject = ['piwikApi'];

    function AlterDeleteController(piwikApi) {
        // remember to keep controller very simple. Create a service/factory (model) if needed
        var self = this;
        var UI = require('piwik/UI');
        this.showDetails = false;

        getVisitorLogsByID = function (id) {
            return piwikApi.fetch({
                id,
                module: 'API',
                method: 'ExtendedPrivacy.getVisitorLogsByID'
            });
        }

        getVisitorLogsCountByID = function (id) {
            return piwikApi.fetch({
                id,
                module: 'API',
                method: 'ExtendedPrivacy.getVisitorLogsCountByID'
            });
        }

        this.getDataForVisitorID = function () {
            Promise.all([
                getVisitorLogsCountByID(this.id)
            ]).then(function (data = []) {
                self.showDetails = true;
                self.entries = data;
                var notification = new UI.Notification();
                notification.show(_pk_translate('ExtendedPrivacy_GenericSuccess'), { context: 'success', id: 'getDataForVisitorID-success' });
                notification.scrollToNotification();
            }, function (error) {
                var notification = new UI.Notification();
                notification.show(_pk_translate('ExtendedPrivacy_GenericError'), { context: 'error', id: 'getDataForVisitorID-error' });
                notification.scrollToNotification();
            }).catch(function (error) {
                var notification = new UI.Notification();
                notification.show(_pk_translate('ExtendedPrivacy_GenericError'), { context: 'error', id: 'getDataForVisitorID-error' });
                notification.scrollToNotification();
            });
        }
    }
})();
