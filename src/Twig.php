<?php

namespace Seven;

use Twig_Environment;
use Twig_Loader_Filesystem;

class Twig
{
    protected $config;
    private $twig;
    protected $filters = [];
    protected $globals = [];

    public function __construct()
    {
        global $config;
        $this->config = $config;
        $TwigLoader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . $this->config->twig->templatesDirectory);
        $this->twig = new Twig_Environment($TwigLoader);
        $this->addGlobal(['alert' => $this->getAlert()]);
        $this->addGlobal(['session' => $_SESSION]);
    }

    protected function addFilter($filter)
    {
        $this->filters[] = $filter;
    }

    protected function loadFilters()
    {
        if (count($this->filters)) {
            foreach ($this->filters as $filter) {
                $this->twig->addFilter($filter);
            }
        }
    }

    protected function addGlobal($array)
    {
        $this->globals = array_merge($this->globals, $array);
    }

    public function render($template, $vars = [])
    {
        return $this->twig->render($template, $this->mergeVars($vars));
    }

    protected function mergeVars($vars)
    {
        return array_merge(
            $this->globals,
            $vars
        );
    }

    protected function getAlert()
    {
        $alert = (isset($_SESSION['alert'])) ? $_SESSION['alert'] : null;
        unset($_SESSION['alert']);
        return $alert;
    }
}
