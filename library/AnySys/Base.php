<?php
namespace AnySys;

/**
 * Class Base
 * @package AnySys
 *
 * @property Database $db
 * @property Config $conf
 * @property View $view
 * @property Request $request
 * @property Session $session
 */
abstract class Base
{
    /** @var array overwriting ok */
    protected $singletons = array( 'conf' => 'Config', 'db' => 'Database', 'view' => 'View', 'session' => 'Session');
    /** @var array overwriting ok */
    protected $instances = array('request' => 'Request');

    /**
     * Base constructor.
     */
    final public function __construct() {
        $this->init();
    }

    /**
     * initialized instances
     */
    protected function init() {
        foreach ($this->singletons as $prop => $cls) {
            $clsName = $this->initClassName($cls);
            $this->{$prop} = $clsName::getInstance();
        }

        foreach ($this->instances as $prop => $cls) {
            $clsName = $this->initClassName($cls);
            $this->{$prop} = new $clsName;
        }
    }

    /**
     * @param $className
     * @return string
     */
    private function initClassName($className) {
        if (0 < strpos('\\', $className)) {
            return $className;
        }
        return __NAMESPACE__ . '\\' . $className;
    }

    /**
     * @return void
     */
    abstract public function execute();
}