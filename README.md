# json-deserializer

Allows deserializing JSON into PHP classes. This library supports deserializing into any class or class hierarchy. 

## Installation

You can install the package via composer

```
composer install yapep/json-deserializer
```

## Usage

Each class you wish to deserialize into needs to have definitions for all

### Using the IJsonDeserializable interface on the target class

If you are in control of the target class, then the easiest way to add the ability to deserialize from a JSON is to make it implement the `IJsonDeserializable` interface.

The following example shows using all 3 basic methods of setting value for a class:
```php
use Yapep\JsonDeserializer\DeserializationProfile;
use Yapep\JsonDeserializer\IJsonDeserializable;
use Yapep\JsonDeserializer\Rule\ScalarRule;

class Foo implements IJsonDeserializable
{
    private string $foo;
    private ?string $bar;
    public ?string $baz;

    public function __construct(string $foo) 
    {
        $this->foo = $foo;
    }

    public function setBar(?string $bar)
    {
        $this->bar = $bar;
    }
    
    public static function getJsonDeserializationProfile() : DeserializationProfile
    {
        return (new DeserializationProfile())
            ->addConstructorRule(new ScalarRule('foo', true, false, ScalarRule::TYPE_STRING))
            ->addSetterRule('setBar', new ScalarRule('bar', true, false, ScalarRule::TYPE_STRING))
            ->addPropertyRule('baz', new ScalarRule('baz', false, true, ScalarRule::TYPE_STRING));
    }
}
```

To deserialize into this class, you can use the following code:

```php
use Yapep\JsonDeserializer\JsonDeserializer;
use Yapep\JsonDeserializer\ProfileRegistry;

$json = '{
    "foo": "fooVal",
    "bar": "barVal",
    "baz": null
}';

$deserializer = new JsonDeserializer(new ProfileRegistry());

$result = $deserializer->deserializeToClassFromJson(Foo::class, $json);
```

### Without modifying the target class

If you are not in control of the target class or it doesn't make sense to add the `IJsonDeserializable` interface to your 
target class then you can register it externally in using the ProfileRegistry.

```php
class Foo
{
    private string $foo;
    private ?string $bar;
    public ?string $baz;

    public function __construct(string $foo) 
    {
        $this->foo = $foo;
    }

    public function setBar(?string $bar)
    {
        $this->bar = $bar;
    }
}
```

To deserialize into this class, you can use the following code:

```php
use Yapep\JsonDeserializer\DeserializationProfile;
use Yapep\JsonDeserializer\JsonDeserializer;
use Yapep\JsonDeserializer\ProfileRegistry;
use Yapep\JsonDeserializer\Rule\ScalarRule;

$json = '{
    "foo": "fooVal",
    "bar": "barVal",
    "baz": null
}';

$profileRegistry = new ProfileRegistry();

$profileRegistry->addProfile(
    Foo::class, 
    (new DeserializationProfile())
        ->addConstructorRule(new ScalarRule('foo', true, false, ScalarRule::TYPE_STRING))
        ->addSetterRule('setBar', new ScalarRule('bar', true, false, ScalarRule::TYPE_STRING))
        ->addPropertyRule('baz', new ScalarRule('baz', false, true, ScalarRule::TYPE_STRING))
);

$deserializer = new JsonDeserializer($profileRegistry);

$result = $deserializer->deserializeToClassFromJson(Foo::class, $json);
```

## Rules

The rules specify how a property of a class is populated from the JSON. All rules must implement the `IRule` interface.
The built in rules are listed below. All rules (except for the StaticValueRule) allow checking if the value (defined by 
`fieldNam`) is present in the JSON data (`isRequired`), and whether they accept `NULL` as the value (`isNullable`).

### StaticValueRule

This rule always returns a static value set during its construction. Useful when for example a static value must be 
passed to a constructor and you don't want to use the advanced methods like the class factory or the callables.

### ScalarRule

This rule parses a scalar value from the JSON and ensures that it's returned as the correct type. Requires specifying 
the scalar's type using the `ScalarRule::TYPE_*` constants.

### DateTimeRule

This rule parses a value into a `DateTime` or `DateTimeImmutable` instance (selectable via `isImmutable`, defaults to 
non immutable). It allows setting the date format (via `formatString`, defaulting to `DateTime::ATOM` or the ISO8601 
format). It also allows optionally passing in a `DateTimeZone` object to set the time zone.

### CarbonRule

Same as the `DateTimeRule` above, except uses the `nesbot/carbon` library (version 2).

### ArrayRule

This rule parses arrays (both regular and associative arrays) and applies a rule to all elements (this may be another 
Array rule for multi-dimensional arrays). It allows validating that the array may be empty or not (via `isEmptyAllowed`).
It also optionally supports unpacking values (via `isUnpacked`) when it is used for a constructor parameter or a setter. 

### ClassRule

This rule parses objects into sub classes. The target class must either implement the `IJsonDeserializable` or be set up 
in the `ProfileRegistry`.

## Profiles

Each class you want to be able to deserialize into must have it's own profile set up either via the `IJsonSerializable` 
interface or via the `ProfileRegistry`. There are 3 simple ways to set values into a class and an advanced method using 
callables.

### Setting values via the constructor

The `addConstructorRule()` method on a profile instance will apply the result of an executed rule as a constructor 
parameter. The parameters are passed to the constructor in the order they are defined in the profile. *The constructor 
rules will not be used when using the class factory (see below)!*

#### Variadic constructors

If a constructor uses a variadic parameter and you expect an array of values to populate it, you can set a parameter to 
be unpacked on it if the rule implements the `IUnpackable` interface. The built in `ArrayRule` implements this interface 
and will allow unpacking the array into the constructor parameter.

### Setting values via public properties

The `addPropertyRule()` method on a profile instance will set the specified property on the class. The property must be 
either public, not defined or you have to have a `__set()` magic method on the class that allows setting this property.
The name of the property must be the first parameter to the `addPropertyRule()` method, while the rule to be set must be 
the second. The property rules are executed after the costructors, and are called in the order that they were defined in 
the profile.

### Setting values via setters

The `addSetterRule()` method on a profile instance will call the specified setter method on the class. The setter method 
must be either public or you have to have a `__call` magic method on the class. The setter method is called with the 
single parameter that's returned by executing the rule (except when using parameter unpacking, see below). The setter's 
return value is discarded. The setters are called after setting all the properties, in the order that they were defined 
in the profile.

#### Variadic setters
When using variadic setters, if the rule implements the `IUnpackable` interface, the values may be unpacked, and through 
that multiple values can be passed to the setter. The built in `ArrayRule` implements this interface and will allow 
unpacking the array into the constructor parameter.

### Setting values via callables for advanced usage
If the above 3 simple methods of setting values is not suitable for your use case, you can use callables to populate the 
target class. The callable will be called with the following parameters:
* the `JsonDeserializer` instance
* the data we are currently processing
* the instance of the class we are currently processing
* the field prefix used for error handling

The return value is not used. The callables are called last and in the order they were defined in the profile. 

## The class factory

Normally the target class is instantiated by simply calling `new Foo` with the constructor parameters parsed from the 
JSON according to the constructor rules. If this is not suitable for your use case, you can set a class factory into the 
profile using a class that implements the `IClassFactory` interface. When using the class factory the constructor 
parameters are not used, but the property, setter and callable methods of the profile are still used after creation.

The class factory can be used as an advanced way to manually deserialize the JSON data.
