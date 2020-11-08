/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

define(['jquery'], function ($) {
    'use strict';

    return {
        /**
         * Prepares the public key options object returned by the Webauthn Framework
         * @param {Object} publicKey
         * @return {Object}
         */
        preparePublicKeyOptions: function (publicKey) {
            //Convert challenge from Base64Url string to Uint8Array
            publicKey.challenge = Uint8Array.from(
                this.base64UrlDecode(publicKey.challenge),
                c => c.charCodeAt(0)
            );

            //Convert the user ID from Base64 string to Uint8Array
            if (publicKey.user !== undefined) {
                publicKey.user = {
                    ...publicKey.user,
                    id: Uint8Array.from(
                        window.atob(publicKey.user.id),
                        c => c.charCodeAt(0)
                    ),
                };
            }

            //If excludeCredentials is defined, we convert all IDs to Uint8Array
            if (publicKey.excludeCredentials !== undefined) {
                publicKey.excludeCredentials = publicKey.excludeCredentials.map(
                    data => {
                        return {
                            ...data,
                            id: Uint8Array.from(
                                this.base64UrlDecode(data.id),
                                c => c.charCodeAt(0)
                            ),
                        };
                    }
                );
            }

            if (publicKey.allowCredentials !== undefined) {
                publicKey.allowCredentials = publicKey.allowCredentials.map(
                    data => {
                        return {
                            ...data,
                            id: Uint8Array.from(
                                this.base64UrlDecode(data.id),
                                c => c.charCodeAt(0)
                            ),
                        };
                    }
                );
            }

            return publicKey;
        },

        /**
         * Decodes a Base64Url string
         * @param {String} input
         * @return {String}
         */
        base64UrlDecode: function (input) {
            input = input
                .replace(/-/g, '+')
                .replace(/_/g, '/');

            const pad = input.length % 4;
            if (pad) {
                if (pad === 1) {
                    throw new Error('InvalidLengthError: Input base64url string is the wrong length to determine padding');
                }
                input += new Array(5-pad).join('=');
            }

            return window.atob(input);
        },

        /**
         * Converts an array of bytes into a Base64Url string
         * @param {String} input
         * @return {String}
         */
        arrayToBase64String: function (a) {
            return btoa(String.fromCharCode(...a));
        },

        /**
         * Prepares the public key credentials object returned by the authenticator
         * @param {Object} input
         * @return {Object}
         */
        preparePublicKeyCredentials: function (data) {
            const publicKeyCredential = {
                id: data.id,
                type: data.type,
                rawId: this.arrayToBase64String(new Uint8Array(data.rawId)),
                response: {
                    clientDataJSON: this.arrayToBase64String(
                        new Uint8Array(data.response.clientDataJSON)
                    ),
                },
            };

            if (data.response.attestationObject !== undefined) {
                publicKeyCredential.response.attestationObject = this.arrayToBase64String(
                    new Uint8Array(data.response.attestationObject)
                );
            }

            if (data.response.authenticatorData !== undefined) {
                publicKeyCredential.response.authenticatorData = this.arrayToBase64String(
                    new Uint8Array(data.response.authenticatorData)
                );
            }

            if (data.response.signature !== undefined) {
                publicKeyCredential.response.signature = this.arrayToBase64String(
                    new Uint8Array(data.response.signature)
                );
            }

            if (data.response.userHandle !== undefined) {
                publicKeyCredential.response.userHandle = this.arrayToBase64String(
                    new Uint8Array(data.response.userHandle)
                );
            }

            return publicKeyCredential;
        }

    };
});
