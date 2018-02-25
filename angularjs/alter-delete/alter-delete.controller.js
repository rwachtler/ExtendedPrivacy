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

        this.searchVisitorByID = function () {
            piwikApi.post({ module: 'API', method: 'ExtendedPrivacy.searchVisitorByID' }, {
                id: this.id,
                format: 'JSON'
            }).then(function (success) {
                var UI = require('piwik/UI');
                var notification = new UI.Notification();
                notification.show(_pk_translate('CoreAdminHome_SettingsSaveSuccess'), { context: 'success', id: 'extendedPrivacySettings' });
                notification.scrollToNotification();
            }, function () {
                // Failure
            })
        }
    }
})();
