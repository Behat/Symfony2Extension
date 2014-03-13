<?php

/*
 * This file is part of the Behat Symfony2Extension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Symfony2Extension\Context\ContextClass;

use Behat\Behat\Context\ContextClass\ClassGenerator;
use Behat\Symfony2Extension\Suite\SymfonyBundleSuite;
use Behat\Testwork\Suite\Suite;

/**
 * @author Christophe Coevoet <stof@notk.org>
 */
final class KernelAwareClassGenerator implements ClassGenerator
{
    /**
     * @var string
     */
    private static $template = <<<'PHP'
<?php

{namespace}use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Behat context class.
 */
class {className} implements SnippetAcceptingContext, KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * Sets Kernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }
}

PHP;

    /**
     * {@inheritdoc}
     */
    public function supportsSuiteAndClass(Suite $suite, $classname)
    {
        return $suite instanceof SymfonyBundleSuite;
    }

    /**
     * {@inheritdoc}
     */
    public function generateClass(Suite $suite, $contextClass)
    {
        $fqn = $contextClass;

        $namespace = '';
        if (false !== $pos = strrpos($fqn, '\\')) {
            $namespace = 'namespace ' . substr($fqn, 0, $pos) . ";\n\n";
            $contextClass = substr($fqn, $pos + 1);
        }

        return strtr(
            static::$template,
            array(
                '{namespace}' => $namespace,
                '{className}' => $contextClass,
            )
        );
    }
}
