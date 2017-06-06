[![Build Status](https://img.shields.io/travis/bauhausphp/middleware-chain/master.svg?style=flat-square)](https://travis-ci.org/bauhausphp/middleware-chain)
[![Coverage Status](https://img.shields.io/coveralls/bauhausphp/middleware-chain/master.svg?style=flat-square)](https://coveralls.io/github/bauhausphp/middleware-chain?branch=master)
[![Codacy Badge](https://img.shields.io/codacy/grade/3d3a045de734470096258238eba79043/master.svg?style=flat-square)](https://www.codacy.com/app/bauhausphp/middleware-chain)

[![Latest Stable Version](https://poser.pugx.org/bauhaus/middleware-chain/v/stable?format=flat-square)](https://packagist.org/packages/bauhaus/middleware-chain)
[![Latest Unstable Version](https://poser.pugx.org/bauhaus/middleware-chain/v/unstable?format=flat-square)](https://packagist.org/packages/bauhaus/middleware-chain)
[![Total Downloads](https://poser.pugx.org/bauhaus/middleware-chain/downloads?format=flat-square)](https://packagist.org/packages/bauhaus/middleware-chain)
[![License](https://poser.pugx.org/bauhaus/middleware-chain/license?format=flat-square)](LICENSE)
[![composer.lock available](https://poser.pugx.org/bauhaus/middleware-chain/composerlock?format=flat-square)](https://packagist.org/packages/bauhaus/middleware-chain)

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
