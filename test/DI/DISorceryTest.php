<?php

namespace Faxity\DI;

use PHPUnit\Framework\TestCase;

/**
 * A DI variant with magic methods..
 */
class DISorceryTest extends TestCase
{
    public function tearDown()
    {
        global $di;
        $di = null;
    }


    /**
     * Tests construction a new instance without any additional sources
     */
    public function testBasicConstruct(): void
    {
        global $di;
        $di = new DISorcery(__DIR__ . "/empty");
        $di->initialize();
        $sources = $di->getSources();
        
        $this->assertIsArray($sources);
        $this->assertCount(1, $sources);
        $this->assertEquals($sources[0], __DIR__ . "/empty");
    }


    /**
     * Tests the autoloading of DI services
     */
    public function testServiceAutoLoading(): void
    {
        $di = new DISorcery(__DIR__);
        $di->initialize();
        $services = $di->getServices();

        $this->assertIsArray($services);
        $this->assertCount(3, $services);
        $this->assertEqualsCanonicalizing($services, ["test1", "test2", "test3"]);
    }


    /**
     * Tests loading configs from sources file.
     */
    public function testLoadSources(): void
    {
        $di = new DISorcery(__DIR__, ANAX_INSTALL_PATH . "/vendor");
        $di->initialize("config/sources.php");
        $sources = $di->getSources();

        $this->assertIsArray($sources);
        $this->assertCount(2, $sources);
        $this->assertEquals($sources[0], __DIR__);
        $this->assertEquals($sources[1], ANAX_INSTALL_PATH . "/vendor/anax/url");
    }


    /*
     * Just for code coverage to see if the loaders are loaded
     */
    public function testPatchLoaders(): void
    {
        global $di;
        $di = new DISorcery(__DIR__ . "/empty", ANAX_INSTALL_PATH . "/vendor");
        $di->initialize(__DIR__ . "/config/sources2.php");
        $services = $di->getServices();

        $this->assertIsArray($services);
        $this->assertCount(2, $services);
        $this->assertEqualsCanonicalizing($services, ["view", "configuration"]);

        // Test load the modules
        $di->get("view");
        $di->get("configuration");
    }


    /**
     * Test the exception when loading of a service fails
     */
    public function testLoadException(): void
    {
        global $di;
        $this->expectException(\Anax\DI\Exception\Exception::class);

        $di = new DISorcery(__DIR__ . "/fail", ANAX_INSTALL_PATH . "/vendor");
        $di->initialize();
    }


    /**
     * Tests loading services from array and from a specific file
     */
    public function testLoadServices(): void
    {
        global $di;
        $di = new DISorcery(__DIR__ . "/empty", ANAX_INSTALL_PATH . "/vendor");
        $di->initialize();

        // Try loading services from array
        $di->loadServices([
            "services" => [
                "hello" => [
                    "callback" => function () {
                        return new \stdClass();
                    }
                ],
            ],
        ]);

        // Test loading file (with and without suffix)
        $di->loadServices(__DIR__ . "/config/di/test.php");
        $di->loadServices(__DIR__ . "/config/di/test");

        $services = $di->getServices();
        $this->assertIsArray($services);
        $this->assertCount(4, $services);
    }
}
