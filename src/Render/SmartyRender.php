<?php

namespace Ufl\Render;

use Exception;
use Smarty;
use SmartyException;
use Ufl\ArrayUtil;
use Ufl\Storage;
use Ufl\System;

/**
 * Class SmartyRender
 * @package Ufl\Render
 */
class SmartyRender implements IRender
{
    /** @var Smarty */
    protected $smarty;
    /** @var string */
    protected $layout;
    /** @var string */
    protected $contentName = 'contents';

    /**
     * SmartyRender constructor.
     */
    public function __construct()
    {
        $this->smarty = new Smarty();
    }

    /**
     * @param array $configs
     * @return void
     */
    public function setConfigs($configs)
    {
        $storage = Storage::getInstance();

        // general
        $templateDir = System::templatePath();
        $this->smarty->setTemplateDir($templateDir);
        $this->smarty->setAutoLiteral(ArrayUtil::get($configs, 'general.auto-literal', true));

        // debug
        $this->smarty->setDebugging(ArrayUtil::get($configs, 'debug.use', false));
        $this->smarty->debugging_ctrl = ArrayUtil::get($configs, 'debug.use-url', false) ? 'URL' : 'NONE';

        // compile
        $this->smarty->setCompileDir($storage->getPath(ArrayUtil::get($configs, 'compile.dir', 'compile'), true));
        $this->smarty->setForceCompile(ArrayUtil::get($configs, 'compile.forced', false));
        $this->smarty->use_sub_dirs = ArrayUtil::get($configs, 'compile.use-sub-dir', true);

        // cache
        $this->smarty->setCaching(ArrayUtil::get($configs, 'cache.use', true));
        $this->smarty->setCacheDir($storage->getPath(ArrayUtil::get($configs, 'cache.dir', 'cache' . DIRECTORY_SEPARATOR . 'template'), true));
        $this->smarty->setCacheLifetime(ArrayUtil::get($configs, 'cache.lifetime', 60));
    }

    /**
     * @param string|array $name
     * @param mixed $var
     * @param bool $noCache
     * @return static
     */
    public function assign($name, $var = null, $noCache = false)
    {
        $this->smarty->assign($name, $var, $noCache);
        return $this;
    }

    /**
     * @param string $template
     * @return string
     * @throws Exception
     * @throws SmartyException
     */
    public function compile($template)
    {
        if ($this->isLayoutMode()) {
            $this->smarty->assign($this->getContentName(), $template);
            return $this->smarty->fetch($this->getLayoutPath());
        }
        return $this->smarty->fetch($template);
    }

    /**
     * @return bool
     */
    public function isLayoutMode()
    {
        return is_string($this->layout);
    }

    /**
     * @return string
     */
    public function getContentName()
    {
        return $this->isLayoutMode() ? $this->contentName : null;
    }

    /**
     * @param $contentName
     * @return void
     */
    public function setContentName($contentName)
    {
        $this->contentName = $contentName;
    }

    /**
     * @return string
     */
    public function getLayoutPath()
    {
        return $this->layout;
    }

    /**
     * @param string $template
     * @return bool
     */
    public function templateExists($template)
    {
        return $this->smarty->templateExists($template);
    }

    /**
     * @param string $layout is template path
     * @return void
     */
    public function setLayoutPath($layout)
    {
        $this->layout = $layout;
    }
}