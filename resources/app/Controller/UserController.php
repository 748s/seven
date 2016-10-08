<?php

namespace App\Controller;

use App\Argument\UserId;
use App\Extension\Controller;
use Seven\FormUtility;

/**
 * Argument
 *
 * @author Nick Wakeman <nick.wakeman@gmail.com>
 * @since  2016-10-08
 */
class UserController extends Controller
{
    public function defaultAction()
    {
        $query = 'SELECT * FROM user ORDER BY firstName ASC';
        $users = $this->db->select($query);
        echo $this->loadTwig()->render('user.default.html.twig', [
            'users' => $users
        ]);
    }

    public function getAction(UserId $userId)
    {
        echo $this->loadTwig()->render('user.get.html.twig', [
            'user' => $this->db->getOneById('user', $userId)
        ]);
    }

    public function addAction()
    {
        $this->showForm();
    }

    public function editAction(UserId $userId)
    {
        $this->showForm($this->db->getOneById('user', $userId));
    }

    private function showForm($user = [], $errors = [])
    {
        echo $this->loadTwig()->render('user.form.html.twig', [
            'user' => $user,
            'errors' => $errors
        ]);
    }

    public function postAction(UserId $userId = null)
    {
        $fu = new FormUtility();
        $fu->isCleanString('firstName', 'First Name is Required');
        $fu->isCleanString('lastName', 'Last Name is Required');
        $fu->isEmailAddress('emailAddress', 'Email Address is required');
        $user = $fu->getData();
        if ($errors = $fu->getErrors()) {
            $this->setFormErrorAlert($errors);
            $this->showForm($user, $errors);
        } else {
            $this->db->put('user', $user, $userId);
            $this->setAlert('alert-success', "You just updated $user[firstName] $user[lastName]\'s record");
            header("Location: /users");
        }
    }

    public function deleteAction(UserId $userId)
    {
        $user = $this->db->getOneById('user', $userId);
        $this->db->deleteOneById('user', $userId);
        $this->setAlert('alert-info', "You just deleted $user[firstName] $user[lastName]");
        header("Location: /users");
    }
}
