<?php
require_once 'UserModel.php';

class UserController
{
    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function index()
    {
        $users = $this->user->getAllUsers();
        require 'views/user_management.php';
    }

    public function store()
    {
        // $data = [
        //     'name' => $_POST['name'],
        //     'address' => $_POST['address'],
        //     'cellphone_number' => $_POST['cellphone_number'],
        //     'phone_number' => $_POST['phone_number'],
        //     'email' => $_POST['email']
        // ];

        // if (!validateUserInput($data)) {
        //     $_SESSION['error'] = 'Error creating user due to validation errors.';
        //     header('Location: index.php');
        //     exit;
        // }

        $result = $this->user->addUser($_POST);

        if ($result) {
            $_SESSION['success'] = 'User created successfully.';
        } else {
            $_SESSION['error'] = 'Error creating user.';
        }

        header('Location: index.php');
        exit;
    }


    public function update()
    {
        $result = $this->user->updateUser($_POST);

        if ($result) {
            $_SESSION['success'] = 'User updated successfully.';
        } else {
            $_SESSION['error'] = 'Error updating user.';
        }

        header('Location: index.php');
        exit;
    }

    public function destroy()
    {
        $result = $this->user->deleteUser($_POST['id']);

        if ($result) {
            $_SESSION['success'] = 'User deleted successfully.';
        } else {
            $_SESSION['error'] = 'Error deleting user.';
        }

        header('Location: index.php');
        exit;
    }
}
