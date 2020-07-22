# Magento 2 Web Authentication Plugin 

The Magento 2 module which provides ability to login using the WebAuthn.

## Holded till the first 2.4.x release due to dependency on Spomky-Labs/otphp version since 9.1.2
- https://devdocs.magento.com/guides/v2.4/release-notes/packages-open-source.html

## Prerequisites
- PHP extension ext-gmp

## Installation

Run the following command at Magento 2 root folder:

```
composer require thesgroup/module-webauthn
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

## Remove the module

Run the following command at Magento 2 root folder:

```
composer remove thesgroup/module-webauthn
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

## Configuration
- https://webauthn-doc.spomky-labs.com/pre-requisites/the-relaying-party


## Authenticators
https://webauthn-doc.spomky-labs.com/webauthn-in-a-nutshell/authenticators

## Web Browser Support
Webauthn is now supported by all main web browsers:
- Mozilla Firefox 60+ and Firefox for Android 68+
- Google Chrome 67+
- Microsoft EDGE 18+ and Microsoft EDGE Chromium 79+
- Opera 54+
- Safari 13+ and iOS Safari 13.3+
- Android Browser 76+

## Libraries
- https://github.com/web-auth/webauthn-lib
