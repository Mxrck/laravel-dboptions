# Laravel DB Options

[![Build Status](https://img.shields.io/travis/Mxrck/laravel-dboptions/master.svg)](https://travis-ci.org/Mxrck/laravel-dboptions/)

Use the database to store key/value options, with preload function and context objects with fallback to global options

### Install

```bash
composer require mxrck/laravel-dboptions
```

### Usage

There are two ways to use the options store, by facade or helpers

#### Simple facade usage

If you only need a database key/value to store data, then you can use the simplest way

```php
// Get an option from the database or fallback to default
Options::get( 'somekey', 'default value' ); // if you need to reload from database then pass a third boolean argument with true

// Update or create (if not exists) an option
Options::update( 'somekey', 'somevalue' ); // If you want to autoload the option then pass a third array argument like ['autoload' => true]

// Check if an option is already stored in the database
Options::exists( 'somekey' );

// Delete an option from the database
Options::remove( 'somekey' );

// Get all options as array
Options::all();

/* this method return an array like this */
$all_options = [
    'somekey' => [
        'value'     => 'THE_VALUE_STORED',
        'autoload'  => false,
        'public'    => false
    ],
    ...
];

// Get all public options as array
Options::public(); // same as before, but only public options
```

### Simple helper usage

```php
// Get options instance
$options = option();
/* With $options you have all facade methods available to use */
/*
 * options()->get(...); options()->update(...); options()->exists(...); ...
 */
 
 // Get and option from database or fallback to default
 option('somekey')
 
 // Update or create (if not exists) an option
 option_update( 'somekey', 'somevalue' );
 
 // Check if option exists
 option_exists( 'somekey' );
 
 // Delete an option
 option_remove( 'somekey' );
```

### Context usage 

*This feature was requested by [@atxy2k](https://github.com/atxy2k)*

If you need a more advanced usage, like set options per user, with fallback to default option system or fallback to a
default value, then you can use an option context. This contexts make use of polymorphic relations to create specific
options, you can make any model to be optionable if you wish.

You need to implement the OptionableInterface, and use the OptionableTrait in the model you want to be optionable like this.

```php
// User.php
use Illuminate\Database\Eloquent\Model;

class User extends Model implements OptionableInterface
{
    use OptionableTrait;
}
```

In the case you customize your morph map, you need to override and extra method in your model

```php
// Provider
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'user' => 'App\User'
]);

// User.php
use Illuminate\Database\Eloquent\Model;

class User extends Model implements OptionableInterface
{
    use OptionableTrait;
    
    public function getType(): string
    {
        return 'user';
    }
}
```

now you can use the facade or helpers with an optionable context like this

```php
$user = User::find(10);

$user->option( 'somekey' ); // By default, from an optionable instance you can call options with the optionable context

// Or you can use the facade and helper with custom context
$value = Option::context( option_context( $user ) )->get( 'somekey' );
// Or
$value = Option::context( Context::make( $user ) )->get( 'somekey' );
// Or
$value = Option::context( $user )->get( 'somekey' );
// Or
$value = option( option_context( $user ) )->get( 'somekey' );
// Or
$value = option( $user )->get( 'somekey' );
``` 

Some examples

```php
option( $user )->get( 'some_option' );
// return null

option( 'some_option');
// return null

option( $user )->update( 'some_option', 'user_value' );
// return 'user_value'
option( $user )->get( 'some_option' );
// return 'user_value'

option()->update( 'some_option', 'system_value' );
// return 'system_value'
option( 'some_option' )
// return 'system_value'

echo 'System: ' option( 'some_option' ) . ' User: '. option( $user )->get( 'some_option' );
// System: system_value User: user_value

// Fallback example:

option()->update( 'color', '#fffff' );
option( option_context( $user ) )->get( 'color' );
// return null
option( option_context( $user, true ) )->get( 'color' );
// return #ffffff

// Or using your model:
$user->optionFallback( 'color' );
// return #ffffff

// By default, context fallback is false
```

There are some hidden features undocumented yet, but the basic usage is here

### Console

#### Create or update an option

```bash
php artisan option:update {KEY} {VALUE}
```

#### Get an option

```bash
php artisan option:get {KEY}
```

#### List all current options

```bash
php artisan option:all
```

### Testing

```bash
composer test
```

### WIP

There are no Context testing yet

### Contributing

Thanks in advance, for all the contributions

### Support me

You can [follow me on Twitter](https://twitter.com/_mxrck), [buy me a coffee](https://www.paypal.me/animechannel/5usd) or [support me on Patreon](https://www.patreon.com/user?u=859275)

### License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.