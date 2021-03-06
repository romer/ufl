<?php

namespace Ufl\Container;

use Ufl\ArrayUtil;

/**
 * Class AbstractContainer
 * @package Ufl\Container
 */
abstract class AbstractContainer implements IContainer
{

    /** @var string */
    protected $prefix;

    /** @var array */
    protected $container;

    /**
     * Session constructor.
     * @param string $prefix
     */
    public function __construct($prefix = null)
    {
        if (is_string($prefix)) {
            $this->prefix = $prefix;
        }
    }

    /**
     * unset session
     * @param string $name
     */
    public function del($name = null)
    {
        $container =& $this->getContainer();
        if (is_null($name)) {
            $container = array();
            return;
        }

        $keys = ArrayUtil::toKeys($name);
        $key = $name;
        if (count($keys) === 1) {
            if (array_key_exists($key, $container)) {
                unset($container[$key]);
            }
            return;
        }

        $lastKey = array_pop($keys);
        $key = ArrayUtil::toKey($keys);
        $setValue = ArrayUtil::get($container, $key, array());
        if (array_key_exists($lastKey, $setValue)) {
            unset($setValue[$lastKey]);
        }

        $this->set($key, $setValue);
    }

    /**
     * @name string
     * @return array
     */
    protected function &getContainer()
    {
        if (!is_array($this->container)) {
            $this->initContainer();
        }
        return $this->container;
    }

    /**
     * init session container
     */
    protected function initContainer()
    {
        $container =& $this->makeContainer();
        if (is_string($this->prefix)) {
            if (!array_key_exists($this->prefix, $container)) {
                $container[$this->prefix] = array();
            }
            $this->container =& $container[$this->prefix];
            return;
        }
        $this->container =& $container;
    }

    abstract protected function &makeContainer();

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        ArrayUtil::set($this->getContainer(), $name, $value);
    }

    /**
     * get session data
     * @param $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return ArrayUtil::get($this->getContainer(), $name, $default);
    }
}