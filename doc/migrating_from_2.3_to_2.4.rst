Migrating from Behat 2.3 to 2.4
===============================

Behat 2.4 brings a lot of extensibility for a price of small backward
compatibility breaks. This guide describes several issues you might
face when updating a Symfony2 project to the latest version of Behat.

There's no BehatBundle nor MinkBundle anymore
---------------------------------------------

Before Behat 2.4 an integration with the Symfony2 framework was done with
bundles. Replacing bundles with extensions is the most important change
for Symfony users introduced in Behat 2.4.

Since Behat got its own extension system there's simply no need for bundles
anymore. So instead of the ``BehatBundle`` you'll need to install the
`Symfony2Extension <http://extensions.behat.org/symfony2/>`_.
``MinkBundle`` was replaced by the `MinkExtension <http://extensions.behat.org/mink/>`_
and several drivers (like `MinkSeleniumDriver <https://github.com/Behat/MinkSeleniumDriver>`_,
`MinkBrowserkitDriver <https://github.com/Behat/MinkBrowserkitDriver>`_).

Here's an example ``composer.json`` snippet taken from a Symfony project using
both ``selenium2`` and ``browserkit`` drivers:

.. code-block:: js

    {
        "require": {
            "behat/behat":  "2.4.*@stable",
            "behat/mink":   "1.4.*@stable",

            "behat/symfony2-extension":      "*",
            "behat/mink-extension":          "*",
            "behat/mink-browserkit-driver":  "*",
            "behat/mink-selenium2-driver":   "*"
        }
    }

.. note::

    Remember to remove initialization of ``BehatBundle`` and ``MinkBundle`` from
    the AppKernel.

Behat configuration is now separated from Symfony
-------------------------------------------------

Instead of configuring Behat in Symfony you'll need to create a new
``behat.yml`` file in the top level directory of your project:

.. code-block:: yaml

    default:
      formatter:
        name: progress
      extensions:
        Behat\Symfony2Extension\Extension:
          mink_driver: true
          kernel:
            env: test
            debug: true
        Behat\MinkExtension\Extension:
          base_url: 'http://www.acme.dev/app_test.php/'
          default_session: symfony2

You'll have to remove your previous configuration (typically placed in
``app/config/config_test.yml``). Otherwise dependency injection container will
complain on unrecognised parameters.

.. note::

    Read more on ``behat.yml`` in the `configuration section <http://docs.behat.org/guides/7.config.html>`_
    of the Behat documentation.

There's no Symfony command anymore
----------------------------------

As the bundles disappeared and configuration has been separated, we have no
Symfony specific command anymore. Behat is now run through its own script.

When using composer it's good to specify the directory you want the commands
to be installed in:

.. code-block:: js

    {
        "config": {
            "bin-dir": "bin"
        }
    }

This way Behat will be accessible via:

.. code-block:: bash

    $ bin/behat

Including autoloader from composer
----------------------------------

If you use composer you'll need to make a small change to the ``app/autoload.php``
file. The ``require_once`` used to include the autoloader needs to be replaced with
a ``require``:

.. code-block:: php

    $loader = require __DIR__.'/../vendor/autoload.php';

Behat already includes Symfony's autoloader and when Symfony tries to include it again
the ``require_once`` returns false instead of the autoloader object.

Accessing the Symfony kernel
----------------------------

If you've been extending ``BehatContext`` from ``BehatBundle`` to get access to
the Symfony kernel you'll need to alter your code and implement the
``KernelAwareInterface`` instead.

The Symfony kernel is injected automatically to every context implementing
the ``KernelAwareInterface``:

.. code-block:: php

    namespace Acme\Bundle\AcmeBundle\Features\Context;

    use Behat\Behat\Context\BehatContext;
    use Behat\Symfony2Extension\Context\KernelAwareInterface;
    use Symfony\Component\HttpKernel\KernelInterface;

    class AcmeContext extends BehatContext implements KernelAwareInterface
    {
        /**
         * @var \Symfony\Component\HttpKernel\KernelInterface $kernel
         */
        private $kernel = null;

        /**
         * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
         *
         * @return null
         */
        public function setKernel(KernelInterface $kernel)
        {
            $this->kernel = $kernel;
        }

        /**
         * @Then /^article should be saved$/
         */
        public function errorShouldBeLogged()
        {
            // access the kernel in your steps
            $doctrine = $this->kernel->getContainer()->get('doctrine');
        }
    }

Accessing Mink session
----------------------

It's possible to inject Mink into the context just like it's possible with the
Symfony kernel. All you need to do is to implement the
`MinkAwareInterface <https://github.com/Behat/MinkExtension/blob/master/src/Behat/MinkExtension/Context/MinkAwareInterface.php>`_.

Alternatively you can extend the `RawMinkContext <https://github.com/Behat/MinkExtension/blob/master/src/Behat/MinkExtension/Context/RawMinkContext.php>`_.
It has an additional benefit of gaining access to several handy methods
(like ``getSession()``, ``assertSession()``, ``getMinkParameter()``).

.. code-block:: php

    namespace Acme\Bundle\AcmeBundle\Features\Context;

    use Behat\MinkExtension\Context\RawMinkContext;

    class AcmeContext extends RawMinkContext
    {
        /**
         * @Given /^I go to (?:|the )homepage$/
         */
        public function iGoToHomepage()
        {
            $this->getSession()->visit('/');
        }
    }

``RawMinkContext`` can be safely extended multiple times since it doesn't
contain any step definitions (as opposed to ``MinkContext``).

To take advantage of steps defined in the ``MinkContext`` you can simply
add it as a subcontext:

.. code-block:: php

    namespace Acme\Bundle\AcmeBundle\Features\Context;

    use Acme\Bundle\AcmeBundle\Features\Context\AcmeContext;
    use Behat\Behat\Context\BehatContext;
    use Behat\MinkExtension\Context\MinkContext;

    class FeatureContext extends BehatContext
    {
        public function __construct()
        {
            $this->useContext('acme', new AcmeContext());
            $this->useContext('mink', new MinkContext());
        }
    }

Assertions
----------

To use PHPUnit's assertions you'll need to include them first:

.. code-block:: php

    require_once 'PHPUnit/Autoload.php';
    require_once 'PHPUnit/Framework/Assert/Functions.php';

It's good for a start but later you'd probably prefer to use new
`WebAssert <https://github.com/Behat/Mink/blob/master/src/Behat/Mink/WebAssert.php>`_
class. Assertions it provides are more suitable for web needs (you should get
more meaningful error messages).

``RawMinkContext`` provides a way to create ``WebAssert`` object with
``assertSession()``:

.. code-block:: php

    namespace Acme\Bundle\AcmeBundle\Features\Context;

    use Behat\MinkExtension\Context\RawMinkContext;

    class AcmeContext extends RawMinkContext
    {
        /**
         * @Then /^I should see an error message$/
         */
        public function iShouldSeeAnErrorMessage()
        {
            $this->assertSession()->elementExists('css', '.error');
        }
    }

Clearing Doctrine's entity manager
----------------------------------

When creating database entries with Doctrine in your contexts you might need to
clear the entity manager before Symfony tries to retrieve any entities:

.. code-block:: php

    $entityManager->clear();

If you store objects in contexts (for future use in other steps) you'll have
to register them back in the entity manager before using (since you removed
them with ``clear()`` call):

.. code-block:: php

    $entityManager->merge($this->page);

