<?php

namespace Faxity\DI;

use Anax\DI\DIMagicTrait;
use Anax\DI\DIFactoryConfig;
use Anax\DI\Exception\Exception;
use Psr\Container\ContainerInterface;

/**
 * Extending DI factory class with magic methods for getters to allow
 * easy usage to $di as $di->service, compare to a $app.
 *
 * This version also includes autoloading of configs, views and DI services.
 */
class DISorcery extends DIFactoryConfig implements ContainerInterface
{
    use DIMagicTrait;

    /** Regex to check if a path is absolute */
    const PATH_REGEX = '~\A[A-Z]:(?![^/\\\\])~i';

    /**
     * @var array  $sources     List of sources to load from
     * @var bool   $initialized If services has been loaded before
     * @var string $appRoot     Anax app root directory
     * @var string $sourcesRoot Directory to load relative source paths
     */
    private $sources = [];
    private $initialized = false;
    private $appRoot;
    private $sourcesRoot;


    /**
     * Resolves a relative path to a custom root, if not absolute
     * @param string $root Root path
     * @param string $path Relative path to resolve
     *
     * @return string
     */
    protected function resolvePath(string $root, string $path)
    {
        return $path[0] !== DIRECTORY_SEPARATOR && preg_match(self::PATH_REGEX, $path) == 0
            ? "$root/$path"
            : $path;
    }


    /**
     * Custom loader for 'configuration' service
     *
     * @return callable
     */
    protected function configLoader(): callable
    {
        return function () {
            $config = new \Anax\Configure\Configuration();
            $dirs = array_reduce($this->sources, function ($paths, $path) {
                if (is_dir("$path/config")) {
                    $paths[] = "$path/config";
                }

                return $paths;
            }, []);

            $config->setBaseDirectories($dirs);
            return $config;
        };
    }


    /**
     * Custom loader for 'view' service.
     *
     * @return callable
     */
    protected function viewLoader(): callable
    {
        $loader = $this->loaded['view']['loader'];

        return function () use ($loader) {
            $view = $loader();
            $dirs = array_reduce($this->sources, function ($paths, $path) {
                if (is_dir("$path/view")) {
                    $paths[] = "$path/view";
                }

                return $paths;
            }, []);

            $view->setPaths($dirs);
            return $view;
        };
    }


    /**
     * Create a service from a name and an array containing details on
     * how to create it.
     * @param string $name    of service.
     * @param array  $service details to use when creating the service.
     *
     * @throws \Anax\DI\Exception\Exception when configuration is corrupt.
     * @return void
     */
    protected function createService(string $name, array $service): void
    {
        if (!isset($service["callback"])) {
            throw new Exception("The service '$name' is missing a callback.");
        }

        if (isset($service["shared"]) && $service["shared"]) {
            $this->setShared($name, $service["callback"]);
        } else {
            $this->set($name, $service["callback"]);
        }

        if (isset($service["active"]) && $service["active"]) {
            if ($this->initialized) {
                $this->get($name);
            } else {
                $this->active[$name] = null; // Set to null to show its not loaded yet
            }
        }
    }


    /**
     * Constructs class instance, loads sources from file if available.
     * @param string      $appRoot     Anax app root directory
     * @param string|null $sourcesRoot (optional) Directory to load relative source paths, defaults to "$appRoot/vendor"
     *
     * @return DISorcery
     */
    public function __construct(string $appRoot, ?string $sourcesRoot = null)
    {
        $this->appRoot = $appRoot;
        $this->sourcesRoot = $sourcesRoot ?? "$appRoot/vendor";
    }


    /**
     * Gets the source folders to load views, config and DI services from.
     *
     * @return array
     */
    public function getSources(): array
    {
        return $this->sources;
    }


    /**
     * Create services by using $item as a reference to find a
     * configuration for the services. The $item can be an array,
     * a file.php, or an directory containing files named *.php.
     *
     * @param array|string $item referencing the source for configuration.
     *
     * @return $this
     */
    public function loadServices($item) : object
    {
        if (is_array($item)) {
            $this->createServicesFromArray($item, "array");
        } else if (is_readable($item) && is_file($item)) {
            $services = require $item;
            $this->createServicesFromArray($services, $item);
        } else {
            if (is_readable("$item.php") && is_file("$item.php")) {
                $services = require "$item.php";
                $this->createServicesFromArray($services, $item);
            }

            if (is_readable($item) && is_dir($item)) {
                foreach (glob("$item/*.php") as $file) {
                    $services = require "$file";
                    $this->createServicesFromArray($services, $file);
                }
            }
        }

        return $this;
    }


    /**
     * Loads sources and initializes the services.
     * @param string|null $sourcesFile (optional) File to load other Anax source directories from.
     *
     * @return void
     */
    public function initialize(?string $sourcesFile = null): void
    {
        $this->initialized = false;
        $sources = [ $this->appRoot ];

        if (is_string($sourcesFile)) {
            $sourcesFile = $this->resolvePath($this->appRoot, $sourcesFile);
            $dirs = require $sourcesFile;

            $sources = array_map(function ($path) {
                return $this->resolvePath($this->sourcesRoot, $path);
            }, array_merge($sources, $dirs));
        }

        // Reverse the order so we overwrite the later services.
        foreach (array_reverse($sources) as $source) {
            $this->loadServices("$source/config/di");
        }

        // Path loaders in services that needs access to sources
        if (array_key_exists('configuration', $this->loaded)) {
            $this->loaded['configuration']['loader'] = $this->configLoader();
        }

        if (array_key_exists('view', $this->loaded)) {
            $this->loaded['view']['loader'] = $this->viewLoader();
        }

        $this->initialized = true;
        $this->sources = $sources;

        // Preload active services
        foreach (array_keys($this->active) as $name) {
            $this->get($name);
        }
    }
}
