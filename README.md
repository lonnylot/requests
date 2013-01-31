# Reqests
### Based on [Kenneth Reitz](https://github.com/kennethreitz) API for [requests](https://github.com/kennethreitz/requests)

## Current Status
In Development

## Examples
```php
$response = (new Get)->request("https://www.lonnylot.com/robots.txt");
var_dump($response->body);
```