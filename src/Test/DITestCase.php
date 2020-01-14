<?php

namespace Faxity\Test;

use Anax\DI\DIMagic;
use Anax\DI\DI;

/**
 * Just a wrapper so we dont need to add same code in all
 * of the DI tests
 */
class DITestCase extends \PHPUnit\Framework\TestCase
{
    /** @var DI $di Dependency injector */
    protected $di;


    /**
     * Creates a DI handler for this test case, by default uses DIMagic.
     *
     * @return DI
     */
    protected function createDI(): DI
    {
        // Create dependency injector with the service
        $di = new DIMagic();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");
        $di->loadServices(ANAX_INSTALL_PATH . "/test/config/di");

        return $di;
    }


    /**
     * Setup for every test case
     *
     * @return void
     */
    public function setUp(): void
    {
        global $di;

        $di = $this->createDI();
        $this->di = $di;
    }


    /**
     * Teardown for every test case
     *
     * @return void
     */
    public function tearDown(): void
    {
        global $di;

        $di = null;
        $this->di = null;
    }
}
