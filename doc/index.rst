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

Installation
------------

This extension requires:

* Behat 2.4+

Through PHAR
~~~~~~~~~~~~

First, download phar archives:

* `behat.phar <http://behat.org/downloads/behat.phar>`_ - Behat itself
* `symfony2_extension.phar <http://behat.org/downloads/symfony2_extension.phar>`_
  - integratino extension

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

    $ php behat.phar --init @YouBundleName

.. note::

    Extension provides alternative ways to specify bundle:

    .. code-block:: bash

        $ php behat.phar --init src/YourCompany/YourBundleName

Run Bundle Suite
~~~~~~~~~~~~~~~~

In order to run feature suite of specific bundle, execute:

.. code-block:: bash

    $ php behat.phar @YouBundleName

.. note::

    Extension provides alternative ways to specify bundle or even
    single feature inside it:

    .. code-block:: bash

        $ php behat.phar @YouBundleName/registration.feature
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

Configuration
-------------

Symfony2Extension comes with flexible configuration system, that gives you ability to
configure Symfony2 kernel inside Behat to fullfil all your needs.

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
