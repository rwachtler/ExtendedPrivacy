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
        this.YouMayOptOut = initTranslation('CoreAdminHome_YouMayOptOut');
        this.ClickHereToOptOut = initTranslation('CoreAdminHome_ClickHereToOptOut');
        this.ClickHereToOptIn = initTranslation('CoreAdminHome_ClickHereToOptIn');
        this.YouMayOptOutBis = initTranslation('CoreAdminHome_YouMayOptOutBis');
        this.YouAreOptedIn = initTranslation('CoreAdminHome_YouAreOptedIn');
        this.YouAreOptedOut = initTranslation('CoreAdminHome_YouAreOptedOut');
        this.OptOutComplete = initTranslation('CoreAdminHome_OptOutComplete');
        this.OptOutCompleteBis = initTranslation('CoreAdminHome_OptOutCompleteBis');
        this.OptedOut = initTranslation('ExtendedPrivacy_OptedOut');
        this.OptOutInfo = initTranslation('ExtendedPrivacy_OptOutInfo');

        this.save = function (key) {
            TransparencyModel.createOrUpdateTranslation(key, self[key])
                .then(data => {
                    if (data.result === 'success') {
                        const notification = new UI.Notification();
                        notification.show(
                            _pk_translate('ExtendedPrivacy_GenericSuccess'),
                            { context: 'success', placeat: '#extended-privacy-notifications-transparency' }
                        );
                        notification.scrollToNotification();
                    }
                })
                .catch(error => {
                    console.error(error);
                    const notification = new UI.Notification();
                    notification.show(
                        _pk_translate('ExtendedPrivacy_GenericError'),
                        { context: 'error', placeat: '#extended-privacy-notifications-transparency' }
                    );
                    notification.scrollToNotification();
                })
        }

        function initTranslation(key) {
            return _pk_translate(key);
        }
    }
})();
