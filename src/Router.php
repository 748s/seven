<?php

namespace Seven;

use InvalidArgumentException;
use ReflectionMethod;

/**
 * Router
 *
 * @author Nick Wakeman <nick.wakeman@gmail.com>
 * @since  2016-10-08
 */
class Router
{
    protected $fqClassName = null;
    protected $methodName = null;
    protected $segments = array();
    protected $requestArguments = [];
    protected $controllerArguments = [];
    protected $Controller;

    public function route()
    {
        $this->segmentRequest();
        if ($this->segments[0] == 'assets') {
            array_shift($this->segments);
            $this->serveAssets();
        } else {
            $this->parseRequest();

            if (!class_exists($this->fqClassName) || !method_exists($this->fqClassName, $this->methodName)) {
                $Controller = Load::extensionOrSeven('Controller');
                $Controller->_404Action();
            } else {
                $this->Controller = new $this->fqClassName();
                if (
                    property_exists($this->fqClassName, 'requireLogin') &&
                    $this->Controller->requireLogin === true && !$this->Controller->isLoggedIn()
                ) {
                    $this->Controller->_401Action();
                } elseif (!$this->verifyParams()) {
                    $this->Controller->_404Action();
                } elseif (
                    method_exists($this->fqClassName, 'getPermission') &&
                    !$this->Controller->getPermission($this->methodName, $this->controllerArguments)
                ) {
                    $this->Controller->_403Action();
                } else {
                    call_user_func_array(array($this->Controller, $this->methodName), $this->controllerArguments);
                }
            }
        }
    }

    protected function segmentRequest()
    {
        $request = explode('?', $_SERVER['REQUEST_URI'])[0];
        $requestArray = explode('/', $request);
        if (count($requestArray) > 0) {
            foreach($requestArray as $segment) {
                if ($segment) {
                    $this->segments[] = $segment;
                }
            }
        }
    }

    // parse request segments into ClassName, methodName, & arguments
    protected function parseRequest()
    {
        $routes = Load::json('./routes.json', true);
        if (count($_POST)) {
            $this->methodName = 'postAction';
            if ($index = array_search('add', $this->segments)) {
                unset($this->segments[$index]);
            } elseif ($index = array_search('edit', $this->segments)) {
                unset($this->segments[$index]);
            }
        } elseif ($index = array_search('add', $this->segments)) {
            $this->methodName = 'addAction';
            unset($this->segments[$index]);
        } elseif ($index = array_search('edit', $this->segments)) {
            $this->methodName = 'editAction';
            unset($this->segments[$index]);
        } elseif ($index = array_search('delete', $this->segments)) {
            $this->methodName = 'deleteAction';
            unset($this->segments[$index]);
        }

        $className = null;
        $directoryClassName = $routes['index'];
        foreach ($this->segments as $index => $segment) {
            if ($segment != '') {
                if (!array_key_exists($segment, $routes)) {
                    $this->requestArguments[] = $segment;
                    unset($this->segments[$index]);
                } else {
                    if (is_array($routes[$segment])) {
                        if (isset($routes[$segment]['index'])) {
                            $directoryClassName = $routes[$segment]['index'];
                        }
                        $routes = $routes[$segment];
                        unset($this->segments[$index]);
                    } else {
                        $className = $routes[$segment];
                        unset($this->segments[$index]);
                    }
                }
            }
        }
        if ($directoryClassName && !$className) {
            $className = $directoryClassName;
        }
        if (!$this->methodName) {
            $this->methodName = (count($this->requestArguments)) ? 'getAction' : 'defaultAction';
        }
        if ($className) {
            $this->fqClassName = '\App\Controller\\' . $className;
        }
    }

    protected function verifyParams()
    {
        $Ref = new ReflectionMethod($this->fqClassName, $this->methodName);
        if (!(
            count($this->requestArguments) >= $Ref->getNumberOfRequiredParameters() &&
            count($this->requestArguments) <= $Ref->getNumberOfParameters()
        )) {
            return false;
        } else {
            $params = $Ref->getParameters();
            foreach($params as $index => $param) {
                if (!$paramClass = $param->getClass()) {
                    $this->controllerArguments[$param->getName()] = $this->requestArguments[$index];
                } else {
                    if (!$param->isOptional() || ($param->isOptional() && isset($this->requestArguments[$index]))) {
                        try {
                            $argument = new $paramClass->name($this->requestArguments[$index]);
                            $this->controllerArguments[$param->getName()] = $argument;
                        } catch (InvalidArgumentException $e) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }
    }

    protected function serveAssets()
    {
        $routes = Load::json('./routes.json', true);
        if (isset($routes['assets']) && is_array($routes['assets']) && count($routes['assets']) > 0) {
            $assets = $routes['assets'];
            $assetString = implode('/', $this->segments);
            $match = false;
            foreach ($assets as $frontEndLocation => $backEndLocation) {
                if (
                    !$match &&
                    $frontEndLocation == substr($assetString, 0, strlen($frontEndLocation)) &&
                    file_exists($backEndLocation . substr($assetString, strlen($frontEndLocation)))
                ) {
                    $file = $backEndLocation . substr($assetString, strlen($frontEndLocation));
                    switch (strtolower(substr($file, strrpos($file, '.') + 1))) {
                        case 'css':
                        $contentType = 'text/css';
                        break;
                        default:
                        $contentType = 'text/plain';
                        break;
                    };
                    header('Content-Description: File Transfer');
                    header("Content-Type: $contentType");
                    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file));
                    readfile($file);
                    $match = true;
                }
            }
            if (!$match) {
                header("HTTP/1.0 404 Not Found");
            }
        } else {
            header("HTTP/1.0 404 Not Found");
        }
    }
}







