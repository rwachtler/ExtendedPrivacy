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
        const self = this;
        const UI = require('piwik/UI');
        this.showDetails = false;
        this.performedRequest = false;
        this.deletionSuccess = false;

        function restoreState() {
            self.showDetails = false;
            self.performedRequest = false;
            self.entries = [];
        }

        this.getData = () => {
            self.deletionSuccess = false;
            AlterDeleteModel.getVisitorLogsCountByID(self.id)
                .then((data = []) => {
                    self.entries = data.filter(entry => entry.quantity > 0);
                    self.showDetails = self.entries.length > 0;
                    self.performedRequest = true;
                    const notification = new UI.Notification();
                    notification.show(
                        _pk_translate('ExtendedPrivacy_GenericSuccess'),
                        { context: 'success', id: 'getDataForVisitorID-success' }
                    );
                    notification.scrollToNotification();
                }).catch(error => {
                    console.error(error);
                    restoreState();
                    const notification = new UI.Notification();
                    notification.show(
                        _pk_translate('ExtendedPrivacy_GenericError'),
                        { context: 'error', id: 'getDataForVisitorID-error' }
                    );
                    notification.scrollToNotification();
                });
        }

        this.deleteAllEntries = () => {
            piwikHelper.modalConfirm('#confirmDeleteAllData', {
                yes: () => {
                    AlterDeleteModel.deleteVisitorLogsByID(self.id)
                        .then((data = []) => {
                            const deletedEntries = data.filter(entry => entry.quantity > 0);
                            let deletionMatch = self.entries.every(entry => {
                                const requestedEntry = self.entries.find(element => element.tableName === entry.tableName);
                                return requestedEntry && requestedEntry.quantity === entry.quantity;
                            });
                            if (deletedEntries.length !== self.entries.length) {
                                deletionMatch = false;
                            }
                            if (deletionMatch) {
                                self.deletionSuccess = true;
                                restoreState();
                            } else {
                                throw new Error('Oops! Looks like the data deletion did not work out as expected! Deletion match was falsy.')
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            restoreState();
                            const notification = new UI.Notification();
                            notification.show(
                                _pk_translate('ExtendedPrivacy_GenericError'),
                                { context: 'error', id: 'deleteVisitorLogsByID-error' }
                            );
                            notification.scrollToNotification();
                        })
                }
            });
        }
    }
})();
