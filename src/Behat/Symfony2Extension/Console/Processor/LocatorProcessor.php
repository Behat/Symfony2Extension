<?php

namespace Behat\Symfony2Extension\Console\Processor;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use Behat\Behat\Console\Processor\LocatorProcessor as BaseProcessor;

/*
 * This file is part of the Behat\Symfony2Extension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Path locator processor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class LocatorProcessor extends BaseProcessor
{
    private $container;

    /**
     * Constructs processor.
     *
     * @param ContainerInterface $container Container instance
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Configures command to be able to process it later.
     *
     * @param Command $command
     */
    public function configure(Command $command)
    {
        $command->addArgument('features', InputArgument::OPTIONAL,
            "Feature(s) to run. Could be:".
            "\n- a dir (<comment>src/to/Bundle/Features/</comment>), " .
            "\n- a feature (<comment>src/to/Bundle/Features/*.feature</comment>), " .
            "\n- a scenario at specific line (<comment>src/to/Bundle/Features/*.feature:10</comment>). " .
            "\n- Also, you can use short bundle notation (<comment>@BundleName/*.feature:10</comment>)"
        );
    }

    /**
     * Processes data from container and console input.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     */
    public function process(InputInterface $input, OutputInterface $output)
    {
        $featuresPath = $input->getArgument('features');
        $pathSuffix   = $this->container->getParameter('behat.symfony2_extension.context.path_suffix');

        $kernel = $this->container->get('behat.symfony2_extension.kernel');
        $bundle = null;

        // get bundle specified in behat.yml
        if ($bundleName = $this->container->getParameter('behat.symfony2_extension.bundle')) {
            $bundle = $kernel->getBundle($bundleName);
        }

        // get bundle from short notation if path starts from @
        if ($featuresPath && preg_match('/^\@([^\/\\\\]+)(.*)$/', $featuresPath, $matches)) {
            $bundle = $kernel->getBundle($matches[1]);
            $featuresPath = str_replace(
                '@'.$bundle->getName(),
                $bundle->getPath().DIRECTORY_SEPARATOR.$pathSuffix,
                $featuresPath
            );
        // get bundle from provided features path
        } elseif (!$bundle && $featuresPath) {
            $path = realpath(preg_replace('/\.feature\:.*$/', '.feature', $featuresPath));
            foreach ($kernel->getBundles() as $kernelBundle) {
                if (false !== strpos($path, realpath($kernelBundle->getPath()))) {
                    $bundle = $kernelBundle;
                    break;
                }
            }
        // if bundle is configured for profile and feature provided
        } elseif ($bundle && $featuresPath) {
            $featuresPath = $bundle->getPath().DIRECTORY_SEPARATOR.$pathSuffix.DIRECTORY_SEPARATOR.$featuresPath;
        }

        if ($bundle) {
            $this->container
                ->get('behat.symfony2_extension.context.class_guesser')
                ->setBundleNamespace($bundle->getNamespace());
        }

        if (!$featuresPath) {
            $featuresPath = $this->container->getParameter('behat.paths.features');
        }

        $this->container
            ->get('behat.console.command')
            ->setFeaturesPaths($featuresPath ? array($featuresPath) : array());
    }
}
