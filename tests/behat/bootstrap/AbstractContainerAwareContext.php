<?php

namespace EM\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Testwork\Hook\Call as Behat;
use EM\Tests\Environment\ContainerAwareTestSuite;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

abstract class AbstractContainerAwareContext extends ContainerAwareTestSuite implements Context, SnippetAcceptingContext
{
    /**
     * @Given setup context
     */
    public function setupContext()
    {
        parent::setUp();
    }

    /**
     * Attempts to guess the kernel location.
     *
     * When the Kernel is located, the file is required.
     *
     * @return string The Kernel class name
     *
     * @throws \RuntimeException
     */
    protected static function getKernelClass()
    {
        if (isset($_SERVER['KERNEL_DIR'])) {
            return parent::getKernelClass();
        }

        $finder = new Finder();
        $finder->name('*Kernel.php')->depth(0)->in(__DIR__ . '/../../../app');
        $results = iterator_to_array($finder);
        if (!count($results)) {
            throw new \RuntimeException('Either set KERNEL_DIR or user default Symfony Structure');
        }

        /**
         * @var File $file
         */
        $file = current($results);
        $class = $file->getBasename('.php');

        require_once $file;

        return $class;
    }
}


