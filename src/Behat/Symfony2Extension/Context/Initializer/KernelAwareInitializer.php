<?php

/*
 * This file is part of the Behat Symfony2Extension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Behat\Symfony2Extension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Kernel aware contexts initializer.
 * Sets Kernel instance to the KernelAware contexts.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class KernelAwareInitializer implements ContextInitializer, EventSubscriberInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * Initializes initializer.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ScenarioTested::BEFORE => array('bootKernel', 15),
            ExampleTested::BEFORE  => array('bootKernel', 15),
            ScenarioTested::AFTER  => array('shutdownKernel', -15),
            ExampleTested::AFTER   => array('shutdownKernel', -15),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initializeContext(Context $context)
    {
        if (!$context instanceof KernelAwareContext && !$this->usesKernelDictionary($context)) {
            return;
        }

        $context->setKernel($this->kernel);
    }

    /**
     * Boots HttpKernel before each scenario.
     */
    public function bootKernel()
    {
        $this->kernel->boot();
    }

    /**
     * Stops HttpKernel after each scenario.
     */
    public function shutdownKernel()
    {
        $this->kernel->shutdown();
    }

    /**
     * Checks whether the context uses the KernelDictionary trait.
     *
     * @param Context $context
     *
     * @return boolean
     */
    private function usesKernelDictionary(Context $context)
    {
        $refl = new \ReflectionObject($context);
        if (method_exists($refl, 'getTraitNames')) {
            if (in_array('Behat\\Symfony2Extension\\Context\\KernelDictionary', $refl->getTraitNames())) {
                return true;
            }
        }

        return false;
    }
}
