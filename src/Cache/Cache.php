<?php

namespace Jegarn\Cache;

use Exception;
use Redis;


class Cache
{
    private static $instance;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    protected $cache;
    protected $config;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        $instance = &self::$instance;
        if ($instance->config !== null && !$instance->cache) {
            ini_set('default_socket_timeout', -1);
            $c = $instance->config;
            $instance->cache = new Redis();
            if (!$instance->cache->connect($c['host'], $c['port'], $c['timeout'])) {
                throw new Exception('cache server connect failed');
            }
            if (isset($c['password']) && trim($c['password']) != "") {
                if (!$instance->cache->auth($c['password'])) {
                    throw new Exception('cache server auth failed');
                }
            }
        }
        return $instance;
    }

    public function initConfig($config)
    {
        $this->config = $config;
    }

    public function destroy()
    {
        if ($this->cache) {
            $this->cache->close();
            $this->cache = null;
        }
    }

    public function __destruct()
    {
        $this->destroy();
    }

    public function scan(&$iterator, $pattern = '', $count = 0)
    {
        return $this->cache->scan($iterator, $pattern, $count);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->cache, $name], $arguments);
    }
}