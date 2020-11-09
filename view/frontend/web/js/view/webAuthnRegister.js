/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

define([
    'jquery',
    'TheSGroup_WebAuthn/js/action/creationRequest',
    'TheSGroup_WebAuthn/js/action/responseVerification',
    'TheSGroup_WebAuthn/js/action/removeDevice',
    'TheSGroup_WebAuthn/js/model/webAuthn',
    'domReady!',
], function (
    $,
    creationRequest,
    responseVerification,
    removeDevice,
    webAuthn
) {
    'use strict';

    $.widget('mage.webAuthnRegister', {
        options: {
            buttonSelector: '#add-new-device',
            formSelector: '#new-device-form',
            nameSelector: 'input#device_name',
            removeSelector: 'button[data-action="remove"]',
            rowSelector: 'tr[data-id="%id%"]',
        },

        /**
         * Widget initialization
         * @private
         */
        _create: function () {
            this._bind();
        },

        /**
         * Bind Elements
         * @private
         */
        _bind: function () {
            let self = this;
            $(this.options.formSelector).submit(function (e) {
                e.preventDefault();
                if ($(self.options.formSelector).validation("isValid")) {
                    $(document.body).trigger('processStart');
                    creationRequest({'deviceName':$(self.options.nameSelector).val()});
                }
            });
            creationRequest.creationRequestRegisterCallback(function (requestData, response) {
                self._creationResponse(response);
            });
            responseVerification.verificationRequestRegisterCallback(function (requestData, response) {
                $(document.body).trigger('processStop');
                location.reload();
            });
            removeDevice.removeDeviceRegisterCallback(function (requestData, response) {
                $(self.options.rowSelector.replace('%id%', requestData.entityId)).remove();
                $(document.body).trigger('processStop');
            });
            this._initRemoveButtons();
        },

        /**
         * Generate Credentials
         * @private
         */
        _creationResponse: function (makeCredentialOptions) {

            console.log("_creationResponse");

            makeCredentialOptions = JSON.parse(makeCredentialOptions);
            let publicKey = webAuthn.preparePublicKeyOptions(makeCredentialOptions);

            navigator.credentials.create({publicKey}).then(function (credentials) {
                let publicKeyCredential = webAuthn.preparePublicKeyCredentials(credentials);
                responseVerification(publicKeyCredential);

            }).catch(function (err) {
                $(document.body).trigger('processStop');
                console.info(err);
            });
        },

        /**
         * Initialize remove buttons
         * @private
         */
        _initRemoveButtons: function () {
            let self = this;
            $(this.options.removeSelector).each(function () {
                $(this).click(function () {
                    self._remove($(this).data('id'));
                });
            });
        },

        /**
         * Remove Credential
         * @param {BigInteger} id
         * @private
         */
        _remove: function (id) {
            $(document.body).trigger('processStart');
            removeDevice({'entityId':id})
        },
    });

    return $.mage.webAuthnRegister;
});
