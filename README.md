# Symfony2Extension

[![Build
Status](https://secure.travis-ci.org/Behat/Symfony2Extension.png?branch=master)](http://travis-ci.org/Behat/Symfony2Extension)

Provides integration layer for Symfony2:

* Complete integration into Symfony2 bundle structure - you can run an isolated
  bundle suite by bundle shortname, classname or even full path
* `KernelAwareInterface`, which provides an initialized and booted kernel instance
  for your contexts
* Additional `symfony2` session (sets as default) for Mink (if `MinkExtension` is installed)

between Behat 2.4+ and Symfony2+

## Installation

This extension requires:

* Behat 2.4+

### Through PHAR

Download Behat phar from:

* [Behat downloads](https://github.com/Behat/Behat/downloads)

After downloading and placing behat.phar into project directory, you need to download and
activate `Symfony2Extension`:

1. [Download extension](https://github.com/downloads/Behat/Symfony2Extension/symfony2_extension.phar)
2. Put downloaded phar package into folder with Behat
3. Tell Behat about extensions with `behat.yml` configuration:

    ``` yaml
    # behat.yml
    default:
      # ...
      extensions:
        symfony2_extension.phar: ~
    ```

    For all configuration options, check [extension configuration
    class](https://github.com/Behat/Symfony2Extension/blob/master/src/Behat/Symfony2Extension/Extension.php#L72-105).

### Through Composer

1. Set dependencies in your `composer.json`:

    ``` json
    {
        "require": {
            ...

            "behat/symfony2-extension": "*"
        }
    }
    ```

2. Install/update your vendors:

    ``` bash
    $> curl http://getcomposer.org/installer | php
    $> php composer.phar install
    ```

3. Activate extension in your `behat.yml`:

    ``` yaml
    # behat.yml
    default:
      # ...
      extensions:
        Behat\Symfony2Extension\Extension: ~
    ```

## Usage

After installing extension, there would be 2 usage options available for you:

1. If you're on the php 5.4+, you can simply use `Behat\Symfony2Extension\Context\KernelDictionary`
   trait inside your `FeatureContext` or any of its subcontexts. This trait will provide
   `getKernel()` and `getContainer()` methods for you.
2. Implementing `Behat\Symfony2Extension\Context\KernelAwareInterface` with your context or its
   subcontexts.
   This will give you more customization options. Also, you can use this mechanism on multiple
   contexts avoiding the need to call parent contexts from subcontexts when the only thing you need
   is a mink instance.

There's a common thing between those 2 methods. In each of those, target context will implement
`setKernel(HttpKernel $kernel)` method. This method would be automatically called **immediately after**
each context creation before each scenario. Note that this kernel will be automatically
rebooted between scenarios, so your scenarios would have almost absolutely isolated state.

## Initialize bundle suite

Just run:

``` bash
$> php behat.phar --init @YouBundleName
```

## Run bundle suite

``` bash
$> php behat.phar @YouBundleName
```

## Using `symfony2` Mink session

Symfony2Extension comes bundled with a custom `symfony2` session for Mink, which is disabled
by default. In order to use it you should download/install/activate MinkExtension and BrowserKit
driver for Mink:

``` json
{
    "require": {
        ...

        "behat/symfony2-extension":      "*",
        "behat/mink-extension":          "*",
        "behat/mink-browserkit-driver":  "*"
    }
}
```

Now just enable `mink_driver` in Symfony2Extension:

``` yaml
# behat.yml
default:
  # ...
  extensions:
    symfony2_extension.phar:
      mink_driver: true
    mink_extension.phar: ~
```

Also, you can make `symfony2` session the default one by setting `default_session` option in
MinkExtension:

``` yaml
# behat.yml
default:
  # ...
  extensions:
    symfony2_extension.phar:
      mink_driver: true
    mink_extension.phar:
      default_session: 'symfony2'
```

## Copyright

Copyright (c) 2012 Konstantin Kudryashov (ever.zet). See LICENSE for details.

## Contributors

* Konstantin Kudryashov [everzet](http://github.com/everzet) [lead developer]
* Other [awesome developers](https://github.com/Behat/Symfony2Extension/graphs/contributors)

## Sponsors

* knpLabs [knpLabs](http://www.knplabs.com/) [main sponsor]
