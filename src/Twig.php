<?php

namespace Seven;

use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;

/**
 * Twig
 *
 * @author Nick Wakeman <nick.wakeman@gmail.com>
 * @since  2016-10-08
 */
class Twig
{
    protected $config;
    protected $twig;
    protected $globals = [];

    public function __construct()
    {
        global $config;
        $this->config = $config;
        $loader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . '/templates');
        if ('development' === $this->config->environment) {
            $this->twig = new Twig_Environment($loader, ['debug' => true]);
            $this->twig->addExtension(new Twig_Extension_Debug());
        } else {
            $this->twig = new Twig_Environment($loader, ['debug' => false]);
        }
        $this->addGlobal(['alert' => $this->getAlert()]);
        $this->addGlobal(['session' => $_SESSION]);
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
