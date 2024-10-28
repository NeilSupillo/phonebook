<?php
function validateUserInput($data)
{
    $errors = [];

    if (empty($data['name'])) {
        $errors['name'] = 'Name is required.';
    }
    if (empty($data['address'])) {
        $errors['address'] = 'Address is required.';
    }
    if (empty($data['cellphone_number']) || !preg_match('/^[0-9]{10,12}$/', $data['cellphone_number'])) {
        $errors['cellphone_number'] = 'Valid cellphone number is required.';
    }
    if (!empty($data['phone_number']) && !preg_match('/^[0-9]{5,10}$/', $data['phone_number'])) {
        $errors['phone_number'] = 'Phone number must be numeric.';
    }
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email is required.';
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }

    return empty($errors);
}
