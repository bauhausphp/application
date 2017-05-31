[![Build Status](https://img.shields.io/travis/bauhausphp/application/master.svg?style=flat-square)](https://travis-ci.org/bauhausphp/application)

# Bauhaus Application

This package helps you to build a [PSR-15 Middleware](https://github.com/php-fig/fig-standards/tree/master/proposed/http-middleware)
chain to process [PSR-7 Server Requests](http://www.php-fig.org/psr/psr-7/#psrhttpmessageserverrequestinterface)
and get [PSR-7 Response](http://www.php-fig.org/psr/psr-7/#psrhttpmessageresponseinterface).

```php
<?php

use Bauhaus\Application;
use SomeVendor\Middleware1;
use AnotherVendor\Middleware2;

$diContainer = require_once 'bootstrap.php'; // Psr\Container\ContainerInterface
$request = require_once 'request.php';

$app = new Application($diContainer);

$app->stackUp(new Middleware1());
$app->stackUp(Middleware2::class); // This will be loaded with $diContainer->get(Middleware2::class)

$response = $app->handle($request);
```
