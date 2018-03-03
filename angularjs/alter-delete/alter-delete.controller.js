/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

(function () {
    angular.module('piwikApp').controller('AlterDeleteController', AlterDeleteController);

    AlterDeleteController.$inject = ['AlterDeleteModel', 'piwikApi'];

    function AlterDeleteController(AlterDeleteModel, piwikApi) {
        // remember to keep controller very simple. Create a service/factory (model) if needed
        var self = this;
        var UI = require('piwik/UI');
        this.showDetails = false;
        this.performedRequest = false;

        this.getData = function () {
            AlterDeleteModel.getDataCountForVisitorID(self.id)
                .then(function (data = []) {
                    self.entries = data.filter(entry => entry.quantity > 0);
                    self.showDetails = self.entries.length > 0;
                    self.performedRequest = true;
                    var notification = new UI.Notification();
                    notification.show(
                        _pk_translate('ExtendedPrivacy_GenericSuccess'),
                        { context: 'success', id: 'getDataForVisitorID-success' }
                    );
                    notification.scrollToNotification();
                }, function (error) {
                    var notification = new UI.Notification();
                    notification.show(
                        _pk_translate('ExtendedPrivacy_GenericError'),
                        { context: 'error', id: 'getDataForVisitorID-error' }
                    );
                    notification.scrollToNotification();
                }).catch(function (error) {
                    var notification = new UI.Notification();
                    notification.show(
                        _pk_translate('ExtendedPrivacy_GenericError'),
                        { context: 'error', id: 'getDataForVisitorID-error' }
                    );
                    notification.scrollToNotification();
                });
        }

        this.deleteAllEntries = function () {
        }
    }
})();
