<?php

/*
 * This file is part of the Behat Symfony2Extension.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Symfony2Extension\ClassGenerator;

use Behat\Behat\Context\ClassGenerator\SimpleContextClassGenerator;
use Behat\Testwork\Suite\Suite;

/**
 * @author Christophe Coevoet <stof@notk.org>
 */
class KernelAwareContextClassGenerator extends SimpleContextClassGenerator
{
    /**
     * @var string
     */
    protected static $template = <<<'PHP'
<?php

{namespace}use Behat\Behat\Context\TurnipAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Behat context class.
 */
class {className} implements TurnipAcceptingContext, KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * Initializes context. Every scenario gets its own context object.
     *
     * @param array $parameters Suite parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
    }

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

    public function supportsSuiteAndClassname(Suite $suite, $classname)
    {
        return $suite->hasSetting('bundle');
    }
}
