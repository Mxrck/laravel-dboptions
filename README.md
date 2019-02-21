# WIP DOCS


### Install

```bash
composer require mxrck/laravel-dboptions
```

### Usage

```php
// With Facade:

// Get an option
Option::get('somekey');
Option::get('somekey', 'default_value');

// Create or update an option
Option::update('somekey', 'somevalue');

// Check if option exists
Option::exists('somekey');

// Remove option if exists
Option::remove('somekey');

// With Helper:

// Get Options instance
option()

// Get an option
$option = option('somekey');
$option = option('somekey', 'default_value');
//  OR
$option = option()->get('somekey');
$option = option()->get('somekey', 'default_value');

// Create or update an option
option_update('somekey', 'somevalue');
option_update('somekey', ['somearrayvalue_one', 'somearrayvalue_two']);
// OR
option()->update('somekey', 'somevalue');
option()->update('somekey', ['somearrayvalue_one', 'somearrayvalue_two']);

// Check if option exists
option_exists('somekey');
// OR
option()->exists('somekey');

// Remove option if exists
option_remove('somekey');
option()->remove('somekey');
```

### Console

#### Create or update an option

```bash
php artisan option:update {KEY} {VALUE}
```

#### Get an option
```bash
php artisan option:get {KEY}
```

### List all current options
```bash
php artisan option:all
```