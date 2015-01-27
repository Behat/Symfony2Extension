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
            ScenarioTested::AFTER  => array('rebootKernel', -15),
            ExampleTested::AFTER   => array('rebootKernel', -15),
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
     * Reboots HttpKernel after each scenario.
     */
    public function rebootKernel()
    {
        $this->kernel->shutdown();
        $this->kernel->boot();
    }

    /**
     * Checks whether the context uses the KernelDictionary trait.
     *
     * @param Context $context
     * @uses detectRecursivelyKernelTrait Description
     * @return boolean
     */
    private function usesKernelDictionary(Context $context)
    {
        $refl = new \ReflectionObject($context);
        
        return $this->detectRecursivelyKernelDictionary($refl);
    }
    
    /**
     * Loop through Traits recursively to find the KernelDictionnary trait
     * 
     * @param \ReflectionClass $refl
     * @return boolean
     * @throws \LogicException if the class is not loadable
     */
    private function detectRecursivelyKernelDictionary(\ReflectionClass $refl)
    {
        foreach($refl->getTraits() as $trait) {
            //trait KernelDictionnary is detected
            if ($trait->name === 'Behat\\Symfony2Extension\\Context\\KernelDictionary') {
                
                return true;
            }
            
            //trait is not detected : we loop through subtraits
            try {
                $subclassReflection = new \ReflectionClass($trait->name);
                
                return $this->detectRecursivelyKernelDictionary($subclassReflection);
            } catch (\ReflectionException $ex) {
                throw new \LogicException(sprintf("The class '%s' could not be loaded", $trait->name), 0, $ex);
            }
        }
        
        return false;
    }
}
