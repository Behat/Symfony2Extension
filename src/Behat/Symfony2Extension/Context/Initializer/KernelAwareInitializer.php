<?php

namespace Behat\Symfony2Extension\Context\Initializer;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\HttpKernel\KernelInterface;

use Behat\Behat\Context\Initializer\InitializerInterface,
    Behat\Behat\Context\ContextInterface,
    Behat\Behat\Event\ScenarioEvent,
    Behat\Behat\Event\OutlineEvent;

use Behat\Symfony2Extension\Context\KernelAwareInterface;

/*
 * This file is part of the Behat\Symfony2Extension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Kernel aware contexts initializer.
 * Sets Kernel instance to the KernelAware contexts.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class KernelAwareInitializer implements InitializerInterface, EventSubscriberInterface
{
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
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            'beforeScenario'       => array('bootKernel', 15),
            'beforeOutlineExample' => array('bootKernel', 15),
            'afterScenario'        => array('shutdownKernel', -15),
            'afterOutlineExample'  => array('shutdownKernel', -15)
        );
    }

    /**
     * Checks if initializer supports provided context.
     *
     * @param ContextInterface $context
     *
     * @return Boolean
     */
    public function supports(ContextInterface $context)
    {
        // if context/subcontext implements KernelAwareInterface
        if ($context instanceof KernelAwareInterface) {
            return true;
        }

        // if context/subcontext uses KernelDictionary trait
        $refl = new \ReflectionObject($context);
        if (method_exists($refl, 'getTraitNames')) {
            if (in_array('Behat\\Symfony2Extension\\Context\\KernelDictionary', $refl->getTraitNames())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Initializes provided context.
     *
     * @param ContextInterface $context
     */
    public function initialize(ContextInterface $context)
    {
        $context->setKernel($this->kernel);
    }

    /**
     * Boots HttpKernel before each scenario.
     *
     * @param ScenarioEvent|OutlineEvent $event
     */
    public function bootKernel($event)
    {
        $this->kernel->boot();
    }

    /**
     * Stops HttpKernel after each scenario.
     *
     * @param ScenarioEvent|OutlineEvent $event
     */
    public function shutdownKernel($event)
    {
        $this->kernel->shutdown();
    }
}
