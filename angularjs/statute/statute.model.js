/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
(function () {
    angular.module('piwikApp.service').factory('StatuteModel', StatuteModel);

    StatuteModel.$inject = ['piwik', '$http'];


    function StatuteModel(piwik, $http) {
        var model = {
            getStatute
        };

        return model;

        /**
         * Currently only for Austria
         */
        function getStatute(source) {
            const title = _pk_translate('ExtendedPrivacy_DataPrivacyAct');
            const version = getFormattedVersionDate();
            const sectionType = _pk_translate('ExtendedPrivacy_SectionType');
            const statuteSource = _pk_translate('ExtendedPrivacy_StatuteSource');
            const options = {
                title,
                version,
                source,
                sectionType,
                statuteSource
            };
            const config = {
                url: 'https://data.bka.gv.at/ris/api/v2.5/Bundesnormen',
                method: 'GET',
                params: {
                    'Titel': title,
                    'Abschnitt.Von': source,
                    'Abschnitt.Bis': source,
                    'Abschnitt.Typ': sectionType,
                    'Kundmachungsorgan': statuteSource,
                    'Fassung.FassungVom': version
                }
            };
            return $http(config).then(response => {
                const { status, statusText = '' } = response || {};

                if (status === 200 && statusText.toLowerCase() === 'ok') {
                    const { data = {} } = response || {};
                    const ogdDocResults = data.OgdSearchResult.OgdDocumentResults;
                    const ogdDocReference = ogdDocResults.OgdDocumentReference;
                    if (ogdDocResults && angular.isArray(ogdDocReference)) {
                        const [htmlStatute] = extractHtmlStatute(data.OgdSearchResult.OgdDocumentResults.OgdDocumentReference[0].Data.Dokumentliste);

                        return htmlStatute;
                    } else if (ogdDocResults) {
                        const [htmlStatute] = extractHtmlStatute(ogdDocReference.Data.Dokumentliste);

                        return htmlStatute;
                    } else if (response.OgdSearchResult.Error) {
                        const { Message: message } = response.OgdSearchResult.Error;
                        throw new Error('API Error', message)
                    }
                }
            }).catch(error => {
                const UI = require('piwik/UI');
                console.error(error);
                const notification = new UI.Notification();
                notification.show(
                    _pk_translate('ExtendedPrivacy_GenericError'),
                    { context: 'error', placeat: '#extended-privacy-notifications-transparency' }
                );
                notification.scrollToNotification();
            });
        }

        function getFormattedVersionDate() {
            const currentDate = Date.now();
            const minimumDate = Date.parse('2018-05-26T00:00:00+01:00');
            const timeDiff = minimumDate - currentDate;
            const unixDate = timeDiff <= 0 ? currentDate : minimumDate;
            const date = new Date(unixDate);
            const day = date.getUTCDate();
            const month = `${date.getUTCMonth() + 1}`.padStart(2, 0);
            const year = date.getUTCFullYear();

            return `${year}-${month}-${day} `;
        }

        function extractHtmlStatute(documentList) {
            if (documentList) {
                const contentUrls = documentList.ContentReference.Urls.ContentUrl;

                return contentUrls
                    .filter(urlItem => urlItem.DataType === 'Html')
                    .map(urlItem => urlItem.Url);
            }
            return '';
        }
    }
})();