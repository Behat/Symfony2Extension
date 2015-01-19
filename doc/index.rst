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

Symfony2Extension is here to replace the obsolete BehatBundle. To migrate
your Behat 2.3 + Symfony2 feature suite,
`read the migration guide </symfony2/migrating_from_2.3_to_2.4.html>`_.

Installation
------------

This extension requires:

* Behat 2.4+

The recommended installation method is through `Composer <http://getcomposer.org>`_:

.. code-block:: bash

    $ composer require behat/symfony2-extension '~1.1'

You can then activate the extension in your ``behat.yml``:

.. code-block:: yaml

    default:
        # ...
        extensions:
            Behat\Symfony2Extension\Extension: ~

.. note::

    Most of the examples in this document show behat being run via ``vendor/bin/behat``,
    which is the default location when installing it through Composer.

Usage
-----

After installing the extension, there are 2 usage options available:

1. If you're using PHP 5.4+, you can simply use the
   ``Behat\Symfony2Extension\Context\KernelDictionary`` trait inside your
   ``FeatureContext`` or any of its subcontexts. This trait will provide the
   ``getKernel()`` and ``getContainer()`` methods for you.

2. Implementing ``Behat\Symfony2Extension\Context\KernelAwareInterface`` with
   your context or its subcontexts. This will give you more customization options.
   Also, you can use this mechanism on multiple contexts avoiding the need to call
   parent contexts from subcontexts when the only thing you need is a kernel instance.

There's a common thing between those 2 methods. In each of those, target context
will implement the ``setKernel(KernelInterface $kernel)`` method. This method would be
automatically called **immediately after** each context creation before each scenario.
After context constructor, but before any instance hook or definition call.

.. note::

    The application kernel will be automatically rebooted between scenarios, so your
    scenarios would have almost absolutely isolated state.

Initialize Bundle Suite
~~~~~~~~~~~~~~~~~~~~~~~

In order to start with your feature suite for specific bundle, execute:

.. code-block:: bash

    $ vendor/bin/behat --init "@YourBundleName"

.. note::

    The extension provides an alternative way to specify the bundle:

    .. code-block:: bash

        $ vendor/bin/behat --init src/YourCompany/YourBundleName

Run Bundle Suite
~~~~~~~~~~~~~~~~

In order to run the feature suite for a specific bundle, execute:

.. code-block:: bash

    $ vendor/bin/behat "@YourBundleName"

.. note::

    The extension provides alternative ways to specify the bundle, or even
    single feature inside it:

    .. code-block:: bash

        $ vendor/bin/behat "@YourBundleName/registration.feature"
        $ vendor/bin/behat src/YourCompany/YourBundleName/Features/registration.feature

If you regularly run the specific bundle suite, it might be useful to use
Behat profiles for that:

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

Now if you need to run the ``UserBundle`` feature suite, you could just execute:

.. code-block:: bash

    $ vendor/bin/behat -p=user

Notice that in this case, you also can avoid bundlename specification for a single
feature run:

.. code-block:: bash

    $ vendor/bin/behat -p=user registration.feature

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

            "behat/symfony2-extension":      "~1.1",
            "behat/mink-extension":          "~1.3",
            "behat/mink-browserkit-driver":  "~1.1"
        }
    }

Now just enable ``mink_driver`` in Symfony2Extension:

.. code-block:: yaml

    default:
        # ...
        extensions:
             Behat\Symfony2Extension\Extension:
                 mink_driver: true
             Behat\MinkExtension\Extension: ~

Also, you can make the ``symfony2`` session the default one by setting ``default_session``
option in MinkExtension:

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

You are not forced to use a bundle-centric structure for your feature suites.
If you want to keep your suite application level, you can simply do it by specifying
a proper ``features`` path and ``context.class`` in your ``behat.yml``:

.. code-block:: yaml

    default:
        paths:
            features: features
        context:
            class:  YourApp\Behat\ContextClass

.. note::

    Keep in mind, that ``Symfony2Extension`` relies on the ``Symfony2`` autoloader for
    context discovery and disables the Behat-bundled autoloader (aka ``bootstrap`` folder).
    So make sure that your context class is discoverable by ``Symfony2`` autoloader
    (place it in proper folder/namespace).

.. note::

    If you're using both ``Symfony2Extension`` and ``MinkExtension`` and have defined
    wrong classname for your context class, you can run into problem where suite
    will still be runnable, but some of your custom definitions/hooks/methods will
    not be available. This happens because ``Behat`` uses the ``MinkExtension``-bundled
    context class instead.

    Here's what's happening:

    1. Behat tries to check existence of FeatureContext class (default) with
       `PredefinedClassGuesser <https://github.com/Behat/Behat/blob/2.5/src/Behat/Behat/Context/ClassGuesser/PredefinedClassGuesser.php>`_
       and obviously can't.
    2. Behat `tries other guessers <https://github.com/Behat/Behat/blob/2.5/src/Behat/Behat/Context/ContextDispatcher.php#L62-66>`_
       with lower priorities.
    3. `There is one
       <https://github.com/Behat/MinkExtension/blob/v1.3.3/src/Behat/MinkExtension/Context/ClassGuesser/MinkContextClassGuesser.php#L20>`_
       defined by ``MinkExtension``, which gets matched and tells Behat to use
       ``Behat\MinkExtension\Context\MinkContext`` as main context class.

    So, your ``FeatureContext`` isn't used, and ``Behat\MinkExtension\Context\MinkContext`` is
    used instead.

    Be sure to check that your suite is run in a proper context (by looking at
    paths next to steps) and that you've defined proper, discoverable context classnames.

Configuration
-------------

Symfony2Extension comes with a flexible configuration system, that gives you the ability to
configure Symfony2 kernel inside Behat to fulfil all your needs.

* ``bundle`` - specifies a bundle to be run for specific profile
* ``kernel`` - specifies options to instantiate the kernel:

  - ``bootstrap`` - defines an autoloading/bootstraping file to autoload
    all the required classes to instantiate the kernel.
  - ``path`` - defines the path to the kernel class in order to instantiate it.
  - ``class`` - defines the name of the kernel class.
  - ``env`` - defines the environment in which kernel should be instantiated and used
    inside suite.
  - ``debug`` - defines whether kernel should be instantiated with ``debug`` option
    set to true.

* ``context`` - specifies options, used to guess the context class:

  - ``path_suffix`` - suffix from bundle directory for features.
  - ``class_suffix`` - suffix from bundle classname for context class.

* ``mink_driver`` - if set to true - extension will load the ``symfony2`` session
  for Mink.
