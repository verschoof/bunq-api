[![Build Status](https://scrutinizer-ci.com/g/verschoof/bunq-api/badges/build.png?b=master)](https://scrutinizer-ci.com/g/verschoof/bunq-api/build-status/master)

[![Build Status](https://travis-ci.org/verschoof/bunq-api.svg?branch=master)](https://travis-ci.org/verschoof/bunq-api)

Symfony
=======

If you use Symfony see https://github.com/verschoof/bunq-api-bundle

Installation
============

Require the package

    composer require verschoof/bunq-api

Now setup the classes in your DI (example https://github.com/verschoof/bunq-api-bundle/blob/master/src/Resources/config/services.yml).

```
<?php

use Bunq\Certificate\Storage\FileCertificateStorage;
use Bunq\HttpClientFactory;
use Bunq\Service\DefaultInstallationService;
use Bunq\Service\DefaultTokenService;
use Bunq\Token\Storage\FileTokenStorage;

include __DIR__ . '/vendor/autoload.php';

$bunqCertificateStorage = new FileCertificateStorage('var/data/bunq');

$bunqHttpInstalltionClient = HttpClientFactory::createInstallationClient(
    'https://sandbox.public.api.bunq.com/v1',
        $bunqCertificateStorage
);

$bunqInstallationService = new DefaultInstallationService(
    $bunqHttpInstalltionClient,
    $bunqCertificateStorage,
    'apiKey',
    ['10.0.0.1']
);

$bunqTokenStorage       = new FileTokenStorage('var/data/bunq');
$bunqTokenService       = new DefaultTokenService($bunqInstallationService, $bunqTokenStorage, $bunqCertificateStorage);

$bunqHttpClient = HttpClientFactory::create($bunqCertificateStorage, $bunqTokenService, $bunqCertificateStorage);

$bunqClient = new \Bunq\Client($bunqHttpClient);

```

Usage
=====

For example to get all users from bunq
```
$userResource = new \Bunq\Resource\UserResource($bunqClient);
$userResource->listUsers();
```

Thats about it.


Todo
====

- [ ] Implement Money object https://github.com/moneyphp/money
