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
            throw new \InvalidArgumentException('Provide features argument in order to init suite.');
        }

        // initialize bundle structure and exit
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
        $featuresPath = $input->getArgument('features');

        $kernel = $this->container->get('behat.symfony2_extension.kernel');
        $bundle = null;

        // get bundle specified in behat.yml
        if ($bundleName = $this->container->getParameter('behat.symfony2_extension.bundle')) {
            $bundle = $kernel->getBundle($bundleName);
        }
        // get bundle from short notation if path starts from @
        if ($featuresPath && preg_match('/^\@([^\/\\\\]+)(.*)$/', $featuresPath, $matches)) {
            $bundle = $kernel->getBundle($matches[1]);
        // get bundle from provided features path
        } elseif ($featuresPath && file_exists($featuresPath)) {
            $featuresPath = realpath($featuresPath);
            foreach ($kernel->getBundles() as $kernelBundle) {
                if (false !== strpos($featuresPath, realpath($kernelBundle->getPath()))) {
                    $bundle = $kernelBundle;
                    break;
                }
            }
        }

        if (null === $bundle) {
            throw new \InvalidArgumentException('Can not find bundle to initialize suite.');
        }

        $featuresPath = $bundle->getPath().DIRECTORY_SEPARATOR.'Features';
        $basePath     = $this->container->getParameter('behat.paths.base').DIRECTORY_SEPARATOR;
        $contextPath  = $featuresPath.DIRECTORY_SEPARATOR.'Context';
        $namespace    = $bundle->getNamespace();

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

use Symfony\Component\HttpKernel\KernelInterface;
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
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
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
