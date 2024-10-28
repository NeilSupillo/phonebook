<?php
session_start();
require_once 'app/UserController.php';

$controller = new UserController();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['addUser'])) {
        $controller->store();
    } elseif (isset($_POST['updateUser'])) {
        $controller->update();
    } elseif (isset($_POST['deleteUser'])) {
        $controller->destroy();
    }
} else {
    $controller->index();
}
