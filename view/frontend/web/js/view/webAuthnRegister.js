/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

define([
    'jquery',
    'TheSGroup_WebAuthn/js/action/creationRequest',
    'TheSGroup_WebAuthn/js/action/responseVerification',
    'TheSGroup_WebAuthn/js/model/webAuthn',
    'domReady!',
], function (
    $,
    creationRequest,
    responseVerification,
    webAuthn
) {
    'use strict';

    $.widget('mage.webAuthnRegister', {
        options: {
            buttonSelector: '#add-new-device',
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
            var self = this;
            $(this.options.buttonSelector).click(function () {
                $(document.body).trigger('processStart');
                creationRequest();
            });
            creationRequest.creationRequestRegisterCallback(function (requestData, response) {
                self._creationResponse(response);
            });
            responseVerification.verificationRequestRegisterCallback(function (requestData, response) {
                console.log('verificationRequestRegisterCallback');
                $(document.body).trigger('processStop');
            });

        },

        /**
         * Generate Credentials
         * @private
         */
        _creationResponse: function (makeCredentialOptions) {

            console.log("_creationResponse");

            makeCredentialOptions = JSON.parse(makeCredentialOptions);
            var publicKey = webAuthn.preparePublicKeyOptions(makeCredentialOptions);

            navigator.credentials.create({publicKey}).then(function (credentials) {
                var publicKeyCredential = webAuthn.preparePublicKeyCredentials(credentials);
                console.log(publicKeyCredential);

                console.log("PublicKeyCredential Created");
                console.log(credentials);
                responseVerification(publicKeyCredential);

            }).catch(function (err) {
                $(document.body).trigger('processStop');
                console.info(err);
            });
        },


    });

    return $.mage.webAuthnRegister;
});
