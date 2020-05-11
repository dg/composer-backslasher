Composer Backslasher
====================

[![Downloads this Month](https://img.shields.io/packagist/dm/dg/composer-backslasher.svg)](https://packagist.org/packages/dg/composer-backslasher)
[![Build Status](https://travis-ci.org/dg/composer-backslasher.svg?branch=master)](https://travis-ci.org/dg/composer-backslasher)

Composer plugin that speeds up your application by adding backslashes to all PHP internal functions and constants in `/vendor`.
Does not modify files of your application.

Installation
------------

```
composer require dg/composer-backslasher
```

Then simply use `composer update`.


How it works?
-------------

It simply turns this code:

```php
namespace A;

if (preg_match('/(foo)(bar)(baz)/', 'foobarbaz', $matches, PREG_OFFSET_CAPTURE)) {
	// ...
}
```

into this code:

```php
namespace A;

if (\preg_match('/(foo)(bar)(baz)/', 'foobarbaz', $matches, \PREG_OFFSET_CAPTURE)) {
	// ...
}
```

to avoid double lookup for global functions and constants.


Configuration
-------------

If you want to ignore certain functions or constants, specify them in the configuration.
Simply add a `extra > backslasher-ignore` section to `composer.json` file:

```js
{
	"extra": {
		"backslasher-ignore": [
			"GuzzleHttp\\Promise\\each"
		]
	}
}
```

Support Project
---------------

Do you like Composer Backslasher? Are you looking forward to the new features?

[![Donate](https://files.nette.org/icons/donation-1.svg?)](https://nette.org/make-donation?to=composer-backslasher)
