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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
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
     * @param string $argument
     *
     * @return mixed
     */
    private function resolveArgument($argument)
    {
        if (is_array($argument)) {
            return array_map(array($this, 'resolveArgument'), $argument);
        }

        if (!is_string($argument)) {
            return $argument;
        }

        $container = $this->kernel->getContainer();
        $container = $container->has('test.service_container') ? $container->get('test.service_container') : $container;

        if ($service = $this->getService($container, $argument)) {
            return $service;
        }

        $resolvedParam = $this->replaceParameters($container, $argument);

        if (!is_string($resolvedParam)) {
            return $resolvedParam;
        }

        return $this->escape($resolvedParam);
    }

    /**
     * @param ContainerInterface $container
     * @param string $argument
     * @return object|null
     * @throws ServiceNotFoundException
     */
    private function getService(ContainerInterface $container, $argument)
    {
        if ($serviceName = $this->getServiceName($argument)) {
            return $container->get($serviceName);
        }

        return null;
    }

    /**
     * @param string $argument
     * @return string|null
     */
    private function getServiceName($argument)
    {
        if (preg_match('/^@[?]?([^@].*)$/', $argument, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Sanitise the container key given by the Behat config file,
     * and retrieve the parameter from the Container.
     *
     * First, check if the whole string is one substitution, if it is, then pull it from the container.
     *
     * Secondly, iterate over all substitutions in the string. Exception is thrown if referencing a
     * collection-type parameter when the key is not an entire substitution.
     *
     * This is to handle the case where we're given an argument which should return a
     * collection-type parameter from the container.
     *
     * @param  ContainerInterface $container
     * @param  string $argument
     * @throws InvalidArgumentException
     * @return mixed
     */
    private function replaceParameters(ContainerInterface $container, $argument)
    {
        if (preg_match('/^(?<!%)%([^%]+)%(?!%)$/', $argument, $matches)) {
            $replaced = $matches[1];

            if ($container->hasParameter($replaced)) {
                return $container->getParameter($replaced);
            }

            return $replaced;
        }
        
        return preg_replace_callback(
            '/(?<!%)%([^%]+)%(?!%)/',
            function ($matches) use ($container) {
                $parameter = $container->getParameter($matches[1]);

                if (is_array($parameter)) {
                    throw new InvalidArgumentException(
                        'Cannot reference a collection-type parameter with string interpolation.'
                    );
                }

                return $parameter;
            },
            $argument
        );
    }

    /**
     * @param string $argument
     * @return string
     */
    private function escape($argument)
    {
        $argument = preg_replace('/^@/', '', $argument);

        return str_replace('%%', '%', $argument);
    }
}
