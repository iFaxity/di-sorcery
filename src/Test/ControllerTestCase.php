<?php

namespace Faxity\Test;

/**
 * Just a wrapper so we dont need to add same code in all
 * of the controllers test classes
 */
class ControllerTestCase extends DITestCase
{
    /**
     * @var \Anax\Commons\ContainerInjectableInterface $controller Anax Controller class
     * @var string $className Controller class name
     */
    protected $controller;
    protected $className;


    /**
     * Setup for every test case
     *
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();
        $controllerClass = $this->className;
        $controller = new $controllerClass();
        $controller->setDI($this->di);

        if (method_exists($controller, "initialize")) {
            $controller->initialize();
        }

        $this->controller = $controller;
    }


    /**
     * Teardown for every test case
     *
     * @return void
     */
    public function tearDown() : void
    {
        parent::tearDown();
        $this->controller = null;
    }
}
