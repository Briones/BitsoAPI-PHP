# BitsoAPI Wrapper for PHP 7  #

A wrapper for the [Bitso® API] (https://bitso.com/api_info/) made in PHP 7 and Symfony 4 

# Motivation #

I'm a Developer that use Bitso as platform for buying and selling Cryptocurrencies, 
and Bitso provides an API in order to create new ways to communicate with theirs systems
so I searched for a composer package for abstract the API requests in PHP and
I found the official [bitso-php library] (https://github.com/bitsoex/bitso-php) but seems 
that this library is made in old an ugly PHP (the kind of PHP that all we hate)
so I created this project in order to try replicate the functionality but with a better implementation
and new technologies, like PHP 7 and Symfony 4. 

I hope this could be useful for someone. 

# Installation #
To install the bitso-api-php api wrapper:
`$ composer require briones/bitso-api-php`
or equivalently in your composer.json file:
```json
{
    "require": {
        "briones/bitsoAPI-php": "master"
    }
}
```

# Public API Usage #

```php

use App\Entity\BitsoPublicApi;

$bitsoPrivateApi = new BitsoPublicApi();...

UNDER CONSTRUCTION