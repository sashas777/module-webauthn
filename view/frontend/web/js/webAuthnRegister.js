/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

define([
    'jquery',
    'domReady!'
], function (
    $
) {
    'use strict';
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
            console.log('webAuthnRegister');
            this._bind();
        },

        /**
         * Bind Elements
         * @private
         */
        _bind: function () {
            $(this.options.buttonSelector).click(function () {
                this._generateCredentials();
            });
        },

        /**
         * Generate Credentials
         * @private
         */
        _generateCredentials: function () {
            console.log("Credential Creation Options");
            console.log(makeCredentialOptions);
            // navigator.credentials.create({
            //     publicKey: makeCredentialOptions.publicKey
            // }).then(function (newCredential) {
            //     console.log("PublicKeyCredential Created");
            //     console.log(newCredential);
            //     state.createResponse = newCredential;
            //     registerNewCredential(newCredential);
            // }).catch(function (err) {
            //     console.info(err);
            // });
        }

    });

    return $.mage.webAuthnRegister;
});
