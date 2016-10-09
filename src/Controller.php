<?php

namespace Seven;

/**
 * Controller
 *
 * @author Nick Wakeman <nick.wakeman@gmail.com>
 * @since  2016-10-08
 */
class Controller
{
    protected $db;
    protected $permission;
    protected $twig;

    public function __construct()
    {
        global $db;
        $this->db = $db;
        $this->permission = Load::extensionOrSeven('Permission');
    }

    public function isLoggedIn()
    {
        return $this->permission->isLoggedIn();
    }

    public function _401Action()
    {
        header("HTTP/1.0 401 Unauthorized");
    }

    public function _403Action()
    {
        header("HTTP/1.0 403 Forbidden");
    }

    public function _404Action()
    {
        header("HTTP/1.0 404 Not Found");
    }

    public function _500Action()
    {
        header("HTTP/1.0 500 Internal Server Error");
    }

    public function setAlert($alertClass, $content, $dismissable = true)
    {
        $_SESSION['alert'] = [
            'alertClass' => $alertClass,
            'content' => $content,
            'dismissable' => $dismissable
        ];
    }

    public function setFormErrorAlert($errors)
    {
        $content = '<ul>';
        foreach ($errors as $error) {
            $content .= "<li>$error</li>";
        }
        $content .= '</ul>';
        $this->setAlert('alert-danger', $content, false);
    }

    protected function loadTwig()
    {
        $this->twig = Load::extensionOrSeven('Twig');
        return $this->twig;
    }
}
