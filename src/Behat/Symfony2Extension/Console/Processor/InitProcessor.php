<?php

namespace Behat\Symfony2Extension\Console\Processor;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use Behat\Behat\Console\Processor\InitProcessor as BaseProcessor;

/*
 * This file is part of the Behat\Symfony2Extension
 *
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Suite initialization processor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class InitProcessor extends BaseProcessor
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
     * {@inheritdoc}
     */
    public function process(InputInterface $input, OutputInterface $output)
    {
        // throw exception if no features argument provided
        if (!$input->getArgument('features') && $input->getOption('init')) {
            throw new \InvalidArgumentException('Provide features argument in order to init suite');
        }

        if ($input->getOption('init')) {
            $this->initBundleDirectoryStructure($input, $output);

            exit(0);
        }
    }

    /**
     * Inits bundle directory structure
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function initBundleDirectoryStructure(InputInterface $input, OutputInterface $output)
    {
        $kernel   = $this->container->get('behat.symfony2_extension.kernel');
        $features = $input->getArgument('features');

        $bundleFound = null;
        if (preg_match('/^\@([^\/\\\\]+)(.*)$/', $features, $matches)) {
            $bundleFound = $kernel->getBundle($matches[1]);
        } else {
            $bundlePath = preg_replace('/Bundle[\/\\\\]Features.*$/', 'Bundle', $features);
            foreach ($kernel->getBundles() as $bundle) {
                if (realpath($bundle->getPath()) === realpath($bundlePath)) {
                    $bundleFound = $bundle;
                    break;
                }
            }
        }

        if (null === $bundleFound) {
            throw new \InvalidArgumentException(
                sprintf('Can not find bundle at path "%s". Have you enabled it?', $bundlePath)
            );
        }

        $featuresPath = $bundlePath.DIRECTORY_SEPARATOR.'Features';
        $basePath     = $this->container->getParameter('behat.paths.base').DIRECTORY_SEPARATOR;
        $contextPath  = $featuresPath.DIRECTORY_SEPARATOR.'Context';
        $namespace    = $bundleFound->getNamespace();

        if (!is_dir($featuresPath)) {
            mkdir($featuresPath, 0777, true);
            $output->writeln(
                '<info>+d</info> ' .
                str_replace($basePath, '', realpath($featuresPath)) .
                ' <comment>- place your *.feature files here</comment>'
            );
        }

        if (!is_dir($contextPath)) {
            mkdir($contextPath, 0777, true);

            file_put_contents(
                $contextPath . DIRECTORY_SEPARATOR . 'FeatureContext.php',
                strtr($this->getFeatureContextSkelet(), array(
                    '%NAMESPACE%' => $namespace
                ))
            );

            $output->writeln(
                '<info>+f</info> ' .
                str_replace($basePath, '', realpath($contextPath)) . DIRECTORY_SEPARATOR .
                'FeatureContext.php <comment>- place your feature related code here</comment>'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getFeatureContextSkelet()
    {
return <<<'PHP'
<?php

namespace %NAMESPACE%\Features\Context;

use Symfony\Bundle\FrameworkBundle\HttpKernel;

use Behat\Symfony2Extension\Context\KernelAwareInterface;

use Behat\MinkExtension\Context\MinkContext;

use Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Feature context.
 */
class FeatureContext extends BehatContext //MinkContext if you want to test web
                  implements KernelAwareInterface
{
    private $kernel;
    private $parameters;

    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param HttpKernel $kernel
     */
    public function setKernel(HttpKernel $kernel)
    {
        $this->kernel = $kernel;
    }

//
// Place your definition and hook methods here:
//
//    /**
//     * @Given /^I have done something with "([^"]*)"$/
//     */
//    public function iHaveDoneSomethingWith($argument)
//    {
//        $container = $this->kernel->getContainer();
//        $container->get('some_service')->doSomethingWith($argument);
//    }
//
}

PHP;
    }
}
