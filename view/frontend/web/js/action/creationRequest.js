/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

define([
    'jquery',
    'mage/storage',
    'Magento_Ui/js/model/messageList',
    'mage/translate'
], function ($, storage, globalMessageList, $t) {
    'use strict';

    var callbacks = [],

        /**
         * @param {Object} requestData
         * @param {*} isGlobal
         * @param {Object} messageContainer
         */
        action = function (requestData, isGlobal, messageContainer) {
            messageContainer = messageContainer || globalMessageList;

            return storage.get(
                'rest/V1/webauthn/creation',
                JSON.stringify(requestData),
                isGlobal
            ).done(function (response) {
                messageContainer.addErrorMessage('asd');
                if (response.errors) {
                    messageContainer.addErrorMessage(response);
                } else {
                    callbacks.forEach(function (callback) {
                        callback(requestData, response);
                    });
                }
            }).fail(function () {
                messageContainer.addErrorMessage({
                    'message': $t('Could not register authenticator. Please try later.')
                });
                $(document.body).trigger('processStop');
            });
        };

    /**
     * @param {Function} callback
     */
    action.creationRequestRegisterCallback = function (callback) {
        callbacks.push(callback);
    };

    return action;
});
