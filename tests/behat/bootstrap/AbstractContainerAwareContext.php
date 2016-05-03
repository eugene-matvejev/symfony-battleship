<?php

namespace EM\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use EM\Tests\Environment\ContainerAwareTestSuite;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

abstract class AbstractContainerAwareContext extends ContainerAwareTestSuite implements Context, SnippetAcceptingContext
{
    /**
     * @var Client
     */
    protected $_client;

    /**
     * @Given request API :route route via :method with :paramKey :paramValue
     *
     * @param string $route
     * @param string $method
     * @param string $paramKey
     * @param string $paramValue
     */
    public function requestApiRouteViaWith(string $route, string $method, string $paramKey, string $paramValue)
    {
        $routeParams = [];
        if (!empty($paramKey) && !empty($paramValue)) {
            $routeParams[$paramKey] = $paramValue;
        }
        $this->_client->request(
            $method,
            static::$router->generate($route, $routeParams),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_accept' => 'application/json']
        );
    }

    /**
     * @Then observe successful response
     */
    public function observeSuccessfulResponse()
    {
        $this->assertSuccessfulResponse($this->_client->getResponse());
    }

    /**
     * @Then observe unsuccessful response
     */
    public function observeUnsuccessfulResponse()
    {
        $this->assertUnsuccessfulResponse($this->_client->getResponse());
    }

    /**
     * @Given setup context
     */
    public function setupContext()
    {
        parent::setUp();

        $this->_client = clone static::$client;
    }

    /**
     * if KERNEL_DIR is not presented change behaviour otherwise use default
     *
     * {@inheritdoc}
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


