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
        $this->loadPermission();
    }

    public function isLoggedIn()
    {
        return $this->permission->isLoggedIn();
    }

    public function _401Action()
    {
        header("HTTP/1.0 401 Unauthorized");
        $this->renderIfTemplateExists('401.default.html.twig');
    }

    public function _403Action()
    {
        header("HTTP/1.0 403 Forbidden");
        $this->renderIfTemplateExists('403.default.html.twig');
    }

    public function _404Action()
    {
        header("HTTP/1.0 404 Not Found");
        $this->renderIfTemplateExists('404.default.html.twig');
    }

    public function _500Action()
    {
        header("HTTP/1.0 500 Internal Server Error");
        $this->renderIfTemplateExists('500.default.html.twig');
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
        $content = '<strong>Your form has is not yet complete:</strong><ul>';
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

    private function loadPermission()
    {
        $this->permission = Load::extensionOrSeven('Permission');
    }

    protected function renderIfTemplateExists($templateName)
    {
        if (file_exists("./templates/$templateName")) {
            echo $this->loadTwig->render($templateName);
        }
    }
}
