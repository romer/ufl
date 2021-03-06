<?php

namespace Ufl;

class Response
{
    /** @var static */
    protected static $instance;
    /** @var Render */
    protected $render;
    /** @var Header */
    private $header;

    /**
     * View constructor.
     */
    protected function __construct()
    {
        $this->header = Header::getInstance();
        $this->header->reset();
    }

    protected function initRender()
    {
        $this->render = Render::getInstance();
        $this->header->add($this->render->getDefaultHeaders());
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!(static::$instance instanceof static)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param string $templatePath
     */
    public function setLayout($templatePath)
    {
        $this->render()->setLayout($templatePath);
    }

    /**
     * @param string|array $name
     * @param mixed $var
     * @param bool $noCache
     * @return static
     */
    public function assign($name, $var = null, $noCache = false)
    {
        $this->render()->assign($name, $var, $noCache);
        return $this;
    }

    /**
     * @param string $template
     */
    public function html($template)
    {
        $this->header()->flush();
        echo $this->compileHtml($template);
    }

    /**
     * @return Header
     */
    public function header()
    {
        return $this->header;
    }

    /**
     * @param string $template
     * @return string
     */
    public function compileHtml($template)
    {
        return $this->render()->compile($template);
    }

    /**
     * @param mixed $data
     * @param string $charset
     */
    public function json($data, $charset = 'utf-8')
    {
        $this->header()->set(array('Content-Type' => 'application/json; charset=' . $charset));
        $this->header()->flush();
        echo json_encode($data);
    }

    /**
     * @param mixed $contents is filepath or raw contents or template path
     * @param string $downloadFileName is local file name
     * @param string $contentType
     */
    public function download($contents, $downloadFileName, $contentType = 'application/octet-stream')
    {
        $isFile = file_exists($contents) && is_readable($contents) && is_file($contents);

        if ($isFile) {
            $size = filesize($contents);
        } else {
            $render = $this->render();
            if ($render->templateExists($contents)) {
                $contents = $render->compile($contents);
            }
            $size = strlen($contents);
        }

        $header = $this->header();
        $encode = mb_detect_encoding($downloadFileName, 'SJIS,SJIS-win,EUC-JP,UTF-8', true);
        if ($encode !== 'UTF-8') {
            $downloadFileName = mb_convert_encoding($downloadFileName, 'UTF-8', $encode);
        }
        $header->set(array(
            'Content-Disposition' => 'attachment; filename*=UTF-8' . "''" . rawurlencode($downloadFileName),
            'Content-Length' => $size,
            'Content-Type' => $contentType
        ));
        $header->flush();
        if ($isFile) {
            readfile($contents);
        } else {
            echo $contents;
        }
    }

    /**
     * @return Render
     */
    private function render()
    {
        if (is_null($this->render)) {
            $this->initRender();
        }
        return $this->render;
    }
}