<?php

namespace Seven;

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
        global $seven;
        $this->segmentRequest();
        $this->parseRequest();

        if (!class_exists($this->fqClassName) || !method_exists($this->fqClassName, $this->methodName)) {
            $Controller = (class_exists('\App\Extension\Controller')) ?
                new \App\Extension\Controller() :
                new \Seven\Controller();
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
        $routes = json_decode(file_get_contents('./routes.json'), true);

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

    private function verifyParams()
    {
        $Ref = new \ReflectionMethod($this->fqClassName, $this->methodName);
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
                        $ArgumentClass = new $paramClass->name();
                        if (!$ArgumentClass->entityExists($this->requestArguments[$index])) {
                            return false;
                        } else {
                            $this->controllerArguments[$param->getName()] = $ArgumentClass;
                        }
                    }
                }
            }
            return true;
        }
    }
}
