# URI builder

This is a simple URI builder in PHP. It allows you to create and manipulate URIs easily. It also fully supports 
repeating query parameters.

## Requirements

This package requires PHP 8.3 or higher.

## Installation

You can install the package via Composer:

```bash
composer require vdhicts/uri-builder
```

## Usage

This package provides three main usages:

- Parsing the URI
- Building the URI
- Manipulating the URI

### Parsing the URI

You can parse a URI using the `Factory` method. This method takes a URI string as input and returns an instance of 
the `Uri` class.

```php
use Vdhicts\UriBuilder\Factory;

$uri = (new Factory())->build('https://example.com/path?query=string#fragment');
```

### Building the URI

You can programmatically build the URI using the `Uri` class.

```php
use Vdhicts\UriBuilder\Uri;

$uri = new Uri(
    scheme: 'https',
    subdomain: 'sub',
    domain: 'example',
    topLevelDomain: 'com',
);
```

Or using the fluent interface:

```php
use Vdhicts\UriBuilder\Uri;

$uri = (new Uri())
    ->setScheme('https')
    ->setSubdomain('sub')
    ->setDomain('example')
    ->setTopLevelDomain('com');
```

To convert the `Uri` object to a string, you can use the `->toString()` method or cast it to a string:

```php
$uri->toString(); // Outputs: https://sub.example.com
(string) $uri; // Outputs: https://sub.example.com
```

### Manipulating the URI

You can manipulate the URI using the `Uri` class. The `Uri` class provides methods to get and set the various 
components of the URI.

```php
use Vdhicts\UriBuilder\Uri;

$uri = new Uri(
    scheme: 'https',
    subdomain: 'sub',
    domain: 'example',
    topLevelDomain: 'com',
);
$uri
    ->setScheme('http')
    ->setSubdomain(null)
    ->setDomain('github'); // Outputs: http://github.com
```

### Note about query parameters

The `Uri` class supports repeating query parameters. This means that you can add the same query parameter multiple times
and it will be added to the URI as a repeating parameter.

```php
use Vdhicts\HttpQueryBuilder\Parameter;
use Vdhicts\UriBuilder\Uri;

$uri = new Uri(
    scheme: 'https',
    subdomain: 'sub',
    domain: 'example',
    topLevelDomain: 'com',
    queryParameters: [
        new Parameter('param', 'value1'),
        new Parameter('param', 'value2'),
    ]   
);
$uri->toString(); // Outputs: https://sub.example.com?param=value1&param=value2
```

Or adding a parameter:

```php
$uri->addQueryParameter(new Parameter('param', 'value3'));
$uri->toString(); // Outputs: https://sub.example.com?param=value1&param=value2&param=value3
```

## Contributing

Found a bug or want to add a new feature? Great! There are also many other ways to make meaningful contributions such
as reviewing outstanding pull requests and writing documentation. Even opening an issue for a bug you found is
appreciated.

When you create a pull request, make sure it is tested, following the code standard (run `composer code-style:fix` to
take care of that for you) and please create one pull request per feature. In exchange, you will be credited as
contributor.

### Testing

To run the tests, you can use the following command:

```bash
composer test
```

### Security

If you discover any security related issues in this or other packages of Vdhicts, please email security@vdhicts.nl
instead of using the issue tracker.
