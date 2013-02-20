Symfony2 Extension
==================

`Symfony2 <http://symfony.com>`_ is a PHP Web Development Framework. This
extension provides integration with it.

Symfony2Extension provides:

* Complete integration into Symfony2 bundle structure - you can run an
  isolated bundle suite by bundle shortname, classname or even full path
* KernelAwareInterface, which provides an initialized and booted kernel
  instance for your contexts
* Additional ``symfony2`` session for Mink (if ``MinkExtension`` is used)

Symfony2Extension is here to replace obsolete BehatBundle. To migrate
your Behat 2.3 + Symfony2 feature suite,
`read migration guide </symfony2/migrating_from_2.3_to_2.4.html>`_.

Installation
------------

This extension requires:

* Behat 2.4+

Through PHAR
~~~~~~~~~~~~

First, download phar archives:

* `behat.phar <http://behat.org/downloads/behat.phar>`_ - Behat itself
* `symfony2_extension.phar <http://behat.org/downloads/symfony2_extension.phar>`_
  - integration extension

After downloading and placing ``*.phar`` into project directory, you need to
activate ``Symfony2Extension`` in your ``behat.yml``:

    .. code-block:: yaml

        default:
          # ...
          extensions:
            symfony2_extension.phar: ~


Through Composer
~~~~~~~~~~~~~~~~

The easiest way to keep your suite updated is to use `Composer <http://getcomposer.org>`_:

1. Define dependencies in your `composer.json`:

    .. code-block:: js

        {
            "require": {
                ...

                "behat/symfony2-extension": "*"
            }
        }

2. Install/update your vendors:

    .. code-block:: bash

        $ curl http://getcomposer.org/installer | php
        $ php composer.phar install

3. Activate extension in your ``behat.yml``:

    .. code-block:: yaml

        default:
            # ...
            extensions:
                Behat\Symfony2Extension\Extension: ~

.. note::

    If you're using Symfony2.1 with Composer, there could be a conflict of
    Symfony2 with Behat, that will prevent Symfony2 from loading Doctrine
    or Validation annotations. This is not a problem with the latest version
    of Composer, but if you are running an older version and see errors,
    just update your ``app/autoload.php``:

    .. code-block:: php

        <?php

        use Doctrine\Common\Annotations\AnnotationRegistry;

        if (!class_exists('Composer\\Autoload\\ClassLoader', false)) {
            $loader = require __DIR__.'/../vendor/autoload.php';
        } else {
            $loader = new Composer\Autoload\ClassLoader();
            $loader->register();
        }

        // intl
        if (!function_exists('intl_get_error_code')) {
            require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

            $loader->add('', __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs');
        }

        AnnotationRegistry::registerLoader('class_exists');

        return $loader;

.. note::

    Most of the examples in this document show behat being run via ``php behat.phar``.
    However, if you install via Composer, you have the option of running via ``/bin/behat``
    instead.  To make this possible, add the following into your `composer.json` before
    installing or updating vendors:
    
    .. code-block:: js
    
        "config": {
            "bin-dir": "bin/"
        },
        
    This will make the ``behat`` command available from the ``/bin`` directory.  If you run
    behat this way, you do not need to download ``behat.phar``.
    
Usage
-----

After installing extension, there would be 2 usage options available for you:

1. If you're on the php 5.4+, you can simply use 
   ``Behat\Symfony2Extension\Context\KernelDictionary`` trait inside your
   ``FeatureContext`` or any of its subcontexts. This trait will provide
   ``getKernel()`` and ``getContainer()`` methods for you.

2. Implementing ``Behat\Symfony2Extension\Context\KernelAwareInterface`` with
   your context or its subcontexts. This will give you more customization options.
   Also, you can use this mechanism on multiple contexts avoiding the need to call
   parent contexts from subcontexts when the only thing you need is a kernel instance.

There's a common thing between those 2 methods. In each of those, target context
will implement ``setKernel(KernelInterface $kernel)`` method. This method would be
automatically called **immediately after** each context creation before each scenario.
After context constructor, but before any instance hook or definition call.

.. note::

    Application kernel will be automatically rebooted between scenarios, so your
    scenarios would have almost absolutely isolated state.

Initialize Bundle Suite
~~~~~~~~~~~~~~~~~~~~~~~

In order to start with your feature suite for specific bundle, execute:

.. code-block:: bash

    $ php behat.phar --init "@YouBundleName"

.. note::

    Extension provides alternative ways to specify bundle:

    .. code-block:: bash

        $ php behat.phar --init src/YourCompany/YourBundleName

Run Bundle Suite
~~~~~~~~~~~~~~~~

In order to run feature suite of specific bundle, execute:

.. code-block:: bash

    $ php behat.phar "@YouBundleName"

.. note::

    Extension provides alternative ways to specify bundle or even
    single feature inside it:

    .. code-block:: bash

        $ php behat.phar "@YouBundleName/registration.feature"
        $ php behat.phar src/YourCompany/YourBundleName/Features/registration.feature

If you run specific bundle suite quite often, it might be useful to
use Behat profile for that:

.. code-block:: yaml

    user:
        # ...
        extensions:
            Behat\Symfony2Extension\Extension:
                bundle: UserBundle

    group:
        # ...
        extensions:
            Behat\Symfony2Extension\Extension:
                bundle: GroupBundle

Now if you need to run ``UserBundle`` feature suite, you could just execute:

.. code-block:: bash

    $ php behat.phar -p=user

Notice that in this case, you also can avoid bundlename specification for single
feature run:

.. code-block:: bash

    $ php behat.phar -p=user registration.feature

This will run ``registration.feature`` tests inside ``UserBundle``.

``symfony2`` Mink Session
~~~~~~~~~~~~~~~~~~~~~~~~~

Symfony2Extension comes bundled with a custom ``symfony2`` session (driver) for Mink,
which is disabled by default. In order to use it you should download/install/activate 
MinkExtension and BrowserKit driver for Mink:

.. code-block:: js

    {
        "require": {
            ...

            "behat/symfony2-extension":      "*",
            "behat/mink-extension":          "*",
            "behat/mink-browserkit-driver":  "*"
        }
    }

Now just enable ``mink_driver`` in Symfony2Extension:

.. code-block:: yaml

    default:
        # ...
        extensions:
             symfony2_extension.phar:
                 mink_driver: true
             mink_extension.phar: ~

Also, you can make ``symfony2`` session the default one by setting ``default_session``
option in MinkExtension:

.. code-block:: yaml

    default:
        # ...
        extensions:
            symfony2_extension.phar:
                mink_driver: true
            mink_extension.phar:
                default_session: 'symfony2'
                
If you have installed via Composer, your ``behat.yml`` would instead look something like the below:

.. code-block:: yaml

    default:
        # ...
        extensions:
            Behat\Symfony2Extension\Extension:
                mink_driver: true
            Behat\MinkExtension\Extension:
                default_session: 'symfony2'

Application Level Feature Suite
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You are not forced to use bundle-centric structure for your feature suites.
If you want to keep your suite application level, you can simply do it by specifying
proper ``features`` path and ``context.class`` in your ``behat.yml``:

.. code-block:: yaml

    default:
        paths:
            features: features
        context:
            class:  YourApp\Behat\ContextClass

.. note::

    Keep in mind, that ``Symfony2Extension`` relies on ``Symfony2`` autoloader for
    context discover and disables Behat bundled autoloader (aka ``bootstrap`` folder).
    So make sure that your context class is discoverable by ``Symfony2`` autoloader
    (place it in proper folder/namespace).

.. note::

    If you're using both ``Symfony2Extension`` and ``MinkExtension`` and have defined
    wrong classname for your context class, you can run into problem where suite
    will still be runnable, but some of your custom definitions/hooks/methods will
    not be available. This happens because ``Behat`` uses bundled with ``MinkExtension``
    context class instead.

    So here's what's happening:

    1. Behat tries to check existence of FeatureContext class (default) with
       `PredefinedClassGuesser <https://github.com/Behat/Behat/blob/master/src/Behat/Behat/Context/ClassGuesser/PredefinedClassGuesser.php>`_
       and obviously can't.
    2. Behat `tries another guessers <https://github.com/Behat/Behat/blob/master/src/Behat/Behat/Context/ContextDispatcher.php#L62-66>`_
       with lower priorities.
    3. `There is one
       <https://github.com/Behat/MinkExtension/blob/master/src/Behat/MinkExtension/Context/ClassGuesser/MinkContextClassGuesser.php#L20>`_
       defined by ``MinkExtension``, which gets matched and tells Behat to use
       ``Behat\MinkExtension\Context\MinkContext`` as main context class.
        
    So, your ``FeatureContext`` isn't used really. ``Behat\MinkExtension\Context\MinkContext``
    used instead.

    So be sure to check that your suite is runned in proper context (by looking at
    paths next to steps) and that you've defined proper, discoverable context classname.

Configuration
-------------

Symfony2Extension comes with flexible configuration system, that gives you ability to
configure Symfony2 kernel inside Behat to fulfil all your needs.

* ``bundle`` - specifies bundle to be runned for specific profile
* ``kernel`` - specifies options to instantiate kernel:

  - ``bootstrap`` - defines autoloading/bootstraping file to autoload
    all the needed classes in order to instantiate kernel.
  - ``path`` - defines path to the kernel class to be requires in order
    to instantiate it.
  - ``class`` - defines name of the kernel class.
  - ``env`` - defines environment in which kernel should be instantiated and used
    inside suite.
  - ``debug`` - defines whether kernel should be instantiated with ``debug`` option
    set to true.

* ``context`` - specifies options, used to guess context class:

  - ``path_suffix`` - suffix from bundle directory for features.
  - ``class_suffix`` - suffix from bundle classname for context class.

* ``mink_driver`` - if set to true - extension will load ``symfony2`` session
  for Mink.
