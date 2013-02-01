# Reqests
### Based on [Kenneth Reitz](https://github.com/kennethreitz) API for [requests](https://github.com/kennethreitz/requests)

## Current Status
In Development

## Examples
### GET Request
```php
<?php

require './vendor/autoload.php';
require 'lib/Requests/Api.php';

$response = Requests\Get("https://www.google.com/robots.txt");
if ($response->isOk()) {
    echo $response->body;
}
```
### POST Request
```php
<?php

require './vendor/autoload.php';
require 'lib/Requests/Api.php';

$response = Requests\Post("https://www.gmail.com/", ["data"=>["foo"=>"bar"]]);
if ($response->isOk()) {
    echo "The response content-type is " . $response->headers["content-type"];
}
```