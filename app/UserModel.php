<?php
class User
{
    private $conn;

    public function __construct()
    {
        try {
            $this->conn = new PDO('mysql:host=localhost;dbname=user_management', 'root', '');
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    public function getAllUsers()
    {
        $stmt = $this->conn->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addUser($data)
    {
        try {
            $stmt = $this->conn->prepare("INSERT INTO users (name, address, cellphone_number, phone_number, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data['name'], $data['address'], $data['cellphone_number'], $data['phone_number'], $data['email']]);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry error code
                return null;
            }
            return 'Error adding user: ' . $e->getMessage();
        }
    }

    public function updateUser($data)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE users SET name = ?, address = ?, cellphone_number = ?, phone_number = ?, email = ? WHERE id = ?");
            $stmt->execute([$data['name'], $data['address'], $data['cellphone_number'], $data['phone_number'], $data['email'], $data['id']]);
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return null;
            }
            return 'Error updating user: ' . $e->getMessage();
        }
    }

    public function deleteUser($id)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            return 'Error deleting user: ' . $e->getMessage();
        }
    }
}
