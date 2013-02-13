# Reqests
### Based on [Kenneth Reitz](https://github.com/kennethreitz) API for [requests](https://github.com/kennethreitz/requests)

## Current Status
Unstable - In Development

## Installation
### Composer


## Examples
### GET Request
```php
<?php

// Require a PSR-0 compliant autoloader
require './vendor/autoload.php';
// Require the Requests API
require 'lib/Requests/Api.php';

$response = Requests\Get("https://www.google.com/robots.txt");
if ($response->isOk()) {
    echo $response->body;
}
```
### POST Request
```php
<?php

// Require a PSR-0 compliant autoloader
require './vendor/autoload.php';
// Require the Requests API
require 'lib/Requests/Api.php';

$response = Requests\Post("https://www.gmail.com/", ["data"=>["foo"=>"bar"]]);
if ($response->isOk()) {
    echo "The response content-type is " . $response->headers["content-type"];
}
```