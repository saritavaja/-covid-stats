<?php


namespace App\DataProvider;


use App\DataProvider\Contracts\DataProviderContract;
use Carbon\Carbon;
use Illuminate\Cache\CacheManager as Cache;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class DataProviderManager
{
    /**
     * The config instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * The active driver instances.
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * The custom connection resolvers.
     *
     * @var array
     */
    protected $extensions = [];

    /**
     * The active driver.
     *
     * @var Request
     */
    protected $driver;

    /**
     * The cache.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Is caching enabled?
     */
    protected $time = null;

    /**
     * Is caching forced?
     *
     * @var Request
     */
    protected $forced = null;

    /**
     * Create a new manager instance.
     *
     * @param Repository $config
     * @param Cache $cache
     * @param Request $request
     */
    public function __construct(Repository $config, Cache $cache, Request $request)
    {
        $this->config = $config;
        $this->cache = $cache;
        $this->request = $request;
    }

    /**
     * Get a connection instance.
     *
     * @param string $name
     *
     * @return DataProviderContract
     */
    public function driver($name = null)
    {
        $name = $this->driver = $name ?: $this->getDefaultDriver();

        if (!isset($this->drivers[$name])) {
            $this->drivers[$name] = $this->makeDriver($name);
        }

        return $this->drivers[$name];
    }

    /**
     * Make the connection instance.
     *
     * @param string $name
     *
     * @return mixed
     */
    protected function makeDriver($name)
    {
        $config = $this->getDriverConfig($name);

        if (isset($this->extensions[$name])) {
            return call_user_func($this->extensions[$name], $config);
        }

        if ($driver = Arr::get($config, 'driver')) {
            if (isset($this->extensions[$driver])) {
                return call_user_func($this->extensions[$driver], $config);
            }
        }

        return $this->createDriver($config);
    }

    /**
     * Get the configuration for a connection.
     *
     * @param string $name
     *
     * @return array
     * @throws \InvalidArgumentException
     *
     */
    public function getDriverConfig($name)
    {
        $name = $this->driver = $name ?: $this->getDefaultDriver();

        $drivers = $this->config->get($this->getConfigName() . '.drivers');

        if (!is_array($config = Arr::get($drivers, $name)) && !$config) {
            throw new \InvalidArgumentException("Driver [$name] not configured.");
        }

        $config['name'] = $name;

        return $config;
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config->get($this->getConfigName() . '.default');
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDriverName()
    {
        return $this->driver ?: $this->getDefaultDriver();
    }

    /**
     * Set the default connection name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setDefaultDriver($name)
    {
        $this->config->set($this->getConfigName() . '.default', $name);

        return $this;
    }

    /**
     * Register an extension driver resolver.
     *
     * @param string $name
     * @param callable $resolver
     *
     * @return void
     */
    public function extend($name, $resolver)
    {
        $this->extensions[$name] = $resolver;
    }

    /**
     * Return all of the created drivers.
     *
     * @return object[]
     */
    public function getDrivers()
    {
        return $this->drivers;
    }

    /**
     * Get the config instance.
     *
     * @return \Illuminate\Contracts\Config\Repository
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($this->time) {
            if ($this->forced) {
                $this->cache->put(serialize($parameters) . md5($method), call_user_func_array([
                    $this->driver(), $method
                ], $parameters), $this->time);
            }

            $response = $this->cache->remember(serialize($parameters) . md5($method), (clone $this->time), function () use ($method, $parameters) {
                $response = call_user_func_array([$this->driver(), $method], $parameters);

                if (!$response) {
                    return false;
                }

                return $response;
            });

            return $response ?: null;
        }

        return call_user_func_array([$this->driver(), $method], $parameters);
    }

    /**
     * Set a cache on the call.
     *
     * @param Carbon $time
     * @param bool $forced
     *
     * @return self
     */
    public function cache(Carbon $time, bool $forced = false)
    {
        $this->time = $time;
        $this->forced = $forced;

        return $this;
    }

    /**
     * Create the connection instance.
     *
     * @param array $config
     *
     * @return DataProviderContract
     */
    protected function createDriver(array $config)
    {
        return $this->make($config);
    }

    /**
     * Create the connection instance.
     *
     * @param array $config
     *
     * @return DataProviderContract
     */
    protected function make(array $config)
    {
        if (!isset($config['name'])) {
            throw new \InvalidArgumentException('A driver must be specified.');
        }

        if (!class_exists($config['adapter']) && is_subclass_of($config['adapter'], DataProviderContract::class)) {
            throw new \InvalidArgumentException("Unsupported driver [{$config['name']}].");
        }

        return app($config['adapter'], [
            'config' => $config,
        ]);
    }

    /**
     * Get the configuration name.
     *
     * @return string
     */
    protected function getConfigName()
    {
        return 'data-provider';
    }
}
