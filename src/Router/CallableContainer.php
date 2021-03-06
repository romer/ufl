<?php

namespace Ufl\Router;

/**
 * Class CallableContainer
 * @package Ufl\Router
 */
class CallableContainer implements IRouterContainer
{
    /**
     * @var callable
     */
    private $context;

    /**
     * @var array
     */
    private $params;

    /**
     * ContextContainer constructor.
     * @param callable $context
     * @param array $params
     */
    public function __construct($context, $params = array())
    {
        $this->context = $context;
        $this->params = $params;
    }

    public function exec()
    {
        if ($this->isValid()) {
            call_user_func_array($this->context, $this->getParams());
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return is_callable($this->context);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }
}