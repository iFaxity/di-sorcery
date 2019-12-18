faxity/di-sorcery
======================

[![Build Status](https://travis-ci.com/iFaxity/di-sorcery.svg?branch=master)](https://travis-ci.com/iFaxity/di-sorcery)
[![Build Status](https://scrutinizer-ci.com/g/iFaxity/di-sorcery/badges/build.png?b=master)](https://scrutinizer-ci.com/g/iFaxity/di-sorcery/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/iFaxity/di-sorcery/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/iFaxity/di-sorcery/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/iFaxity/di-sorcery/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/iFaxity/di-sorcery/?branch=master)

This is an extension for Anax to load configuration, views and DI modules directly from the vendor folder.

## Installation

To install the package using composer:

`composer require faxity/di-sorcery`

Then after that you need to update the `htdocs/index.php` file to use DISorcery like this:

```php
// Replace or comment out the current DIFactory config
// Add all framework services to $di
//$di = new Anax\DI\DIFactoryConfig();
//$di->loadServices(ANAX_INSTALL_PATH . "/config/di");

// Add all framework services to $di
$di = new \Faxity\DI\DISorcery(ANAX_INSTALL_PATH, ANAX_INSTALL_PATH . "/vendor");
$di->initialize("config/sorcery.php");
```

Then create the file `sorcery.php` in the `config` folder.
In this file you can enter the paths where configuration, views and DI modules are resolved from.
A relative path is resolved to the path in the second argument of the constructor, or first argument + "/vendor".

```php
<?php

/**
 * Configuration file for Anax sources, all relative paths are vendor scoped.
 */
return [
    "anax/cache",
    "anax/configure",
    "anax/content",
    "anax/database",
    "anax/database-query-builder",
    "anax/page",
    "anax/request",
    "anax/response",
    "anax/router",
    "anax/session",
    "anax/textfilter",
    "anax/url",
    "anax/view",
];
```
