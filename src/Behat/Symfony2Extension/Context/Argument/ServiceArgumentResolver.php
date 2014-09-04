<?php

/*
 * This file is part of the Behat Symfony2Extension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Behat\Symfony2Extension\Context\Argument;

use Behat\Behat\Context\Argument\ArgumentResolver;
use ReflectionClass;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Resolves service arguments using the application container.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class ServiceArgumentResolver implements ArgumentResolver
{
    private $kernel;

    /**
     * Initializes resolver.
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
    public function resolveArguments(ReflectionClass $classReflection, array $arguments)
    {
        $newArguments = array();

        foreach ($arguments as $key => $argument) {
            $newArguments[$key] = $this->resolveArgument($argument);
        }

        return $newArguments;
    }

    /**
     * Resolves single argument using container.
     *
     * @param mixed $argument
     *
     * @return object
     */
    private function resolveArgument($argument)
    {
        $container = $this->kernel->getContainer();

        if (!is_string($argument) || '@' != $argument[0]) {
            return $argument;
        }

        $serviceId = mb_substr($argument, 1, mb_strlen($argument, 'utf8'), 'utf8');

        return $container->has($serviceId) ? $container->get($serviceId) : $argument;
    }
}
