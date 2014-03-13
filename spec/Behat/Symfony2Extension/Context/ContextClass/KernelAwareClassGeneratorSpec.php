<?php

namespace spec\Behat\Symfony2Extension\Context\ContextClass;

use Behat\Symfony2Extension\Suite\SymfonyBundleSuite;
use Behat\Testwork\Suite\Suite;
use PhpSpec\ObjectBehavior;

class KernelAwareClassGeneratorSpec extends ObjectBehavior
{
    function it_is_a_class_generator()
    {
        $this->shouldHaveType('Behat\Behat\Context\ContextClass\ClassGenerator');
    }

    function it_supports_symfony_suites(SymfonyBundleSuite $suite)
    {
        $this->supportsSuiteAndClass($suite, 'test\classname')->shouldBe(true);
    }

    function it_does_not_support_other_suites(Suite $suite)
    {
        $this->supportsSuiteAndClass($suite, 'test\classname')->shouldBe(false);
    }

    function it_generates_classes_in_the_global_namespace(SymfonyBundleSuite $suite)
    {
        $code = <<<'PHP'
<?php

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Behat context class.
 */
class TestContext implements SnippetAcceptingContext, KernelAwareContext
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
        $this->generateClass($suite, 'TestContext')->shouldReturn($code);
    }

    function it_generates_namespaced_classes(SymfonyBundleSuite $suite)
    {
        $code = <<<'PHP'
<?php

namespace Test;

use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Behat context class.
 */
class TestContext implements SnippetAcceptingContext, KernelAwareContext
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
        $this->generateClass($suite, 'Test\TestContext')->shouldReturn($code);
    }
}
