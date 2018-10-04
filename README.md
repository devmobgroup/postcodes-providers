# Postcodes providers

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

### PostcodeAPI.nu

##### Installation
```
composer require devmobgroup/postcodes-postcode-api-nu
```

##### Usage
First you should [obtain an API key](https://www.postcodeapi.nu/#pakketten).

```php
use DevMob\Postcodes\Providers\PostcodeApiNu\PostcodeApiNu;

$provider = new PostcodeApiNu([
    'key' => 'YOUR_API_KEY',
]);
$address = $provider->lookup('3011ED', '50');
```

### API Postcode

##### Installation
```
composer require devmobgroup/postcodes-api-postcode
```

##### Usage
A token is required, [obtain one here](https://api-postcode.nl/register).
```php
use DevMob\Postcodes\Providers\ApiPostcode\ApiPostcode;

$provider = new ApiPostcode([
    'token' => 'YOUR_TOKEN',
]);
$address = $provider->lookup('3011ED', '50');
```