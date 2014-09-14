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
        $container = $this->kernel->getContainer();

        if ($service = $this->getService($container, $argument)) {
            return $service;
        }

        if ($parameter = $this->getParameter($container, $argument)) {
            return $parameter;
        }

        return $this->escape($argument);
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
        if (preg_match('/^@([^@].*)$/', $argument, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @param ContainerInterface $container
     * @param string $argument
     * @return string|null
     * @throws ParameterNotFoundException
     */
    private function getParameter(ContainerInterface $container, $argument)
    {
        if ($argumentName = $this->getParameterName($argument)) {
            return $container->getParameter($argumentName);
        }

        return null;
    }

    /**
     * @param string $argument
     * @return string|null
     */
    private function getParameterName($argument)
    {
        if (preg_match('/^%(.*)%$/', $argument, $matches)) {
            return $matches[1];
        }

        return null;
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
