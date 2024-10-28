<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "user_management";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error and success messages
$errors = [];
$success = '';

// Handle Add User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addUser'])) {

    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $cellphoneNumber = $_POST['cellphone_number'] ?? '';
    $phoneNumber = $_POST['phone_number'] ?? '';
    $email = $_POST['email'] ?? '';

    // Validate input
    if (empty($name) || empty($address) || empty($cellphoneNumber) || empty($email)) {
        $errors['form'] = 'All fields except phone number are required.';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors['email'] = 'Email already exists.';
        } else {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, address, cellphone_number, phone_number, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $address, $cellphoneNumber, $phoneNumber, $email);
            if ($stmt->execute()) {
                $success = 'User added successfully.';
            } else {
                $errors['form'] = 'Error adding user.';
            }
        }
    }
}

// Handle Edit User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateUser'])) {

    $id = $_POST['id'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $cellphoneNumber = $_POST['cellphone_number'];
    $phoneNumber = $_POST['phone_number'];
    $email = $_POST['email'];

    // Validate input
    if (empty($name) || empty($address) || empty($cellphoneNumber) || empty($email)) {
        $errors['form'] = 'All fields except phone number are required.';
    } else {
        // Check if email is already used by another user
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors['email'] = 'Email already exists.';
        } else {
            // Update user
            $stmt = $conn->prepare("UPDATE users SET name = ?, address = ?, cellphone_number = ?, phone_number = ?, email = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $name, $address, $cellphoneNumber, $phoneNumber, $email, $id);
            if ($stmt->execute()) {
                $success = 'User updated successfully.';
            } else {
                $errors['form'] = 'Error updating user.';
            }
        }
    }
}

// Handle Delete User
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['deleteUser'])) {

    $id = $_POST['id'];

    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'User deleted successfully.';
    } else {
        $errors['form'] = 'Error deleting user.';
    }
}

// Fetch all users
$users = [];
$result = $conn->query("SELECT * FROM users");
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">

        <!-- Success Message -->
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p class="mb-0"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Add User Form (40% width) -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3>Add User</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Name" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" placeholder="Address" required>
                            </div>
                            <div class="mb-3">
                                <label for="cellphone_number" class="form-label">Cellphone Number</label>
                                <input type="text" name="cellphone_number" class="form-control" placeholder="09344334242" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" placeholder="32034">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="example@example.com" required>
                            </div>
                            <button type="submit" name="addUser" class="btn btn-primary">Add User</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- User List Table (60% width) -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>User List</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <!-- <th scope="col">#</th> -->
                                    <th scope="col">Name</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Cellphone Number</th>
                                    <th scope="col">Phone Number</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <!-- <td><input type="checkbox" name="user_ids[]" value="<?= $user['id'] ?>"></td> -->
                                        <td><?= htmlspecialchars($user['name']) ?></td>
                                        <td><?= htmlspecialchars($user['address']) ?></td>
                                        <td><?= htmlspecialchars($user['cellphone_number']) ?></td>
                                        <td><?= htmlspecialchars($user['phone_number']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <!-- Edit Button (Modal Trigger) -->
                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editModal<?= $user['id'] ?>">Edit
                                                </button>

                                                <!-- Delete Form -->
                                                <form method="POST" action="" style="display:inline;"
                                                    onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                    <button type="submit" name="deleteUser" class="btn btn-danger btn-sm">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>


                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal<?= $user['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $user['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editModalLabel<?= $user['id'] ?>">Edit User</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                                        <div class="mb-3">
                                                            <label for="name" class="form-label">Name</label>
                                                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="address" class="form-label">Address</label>
                                                            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($user['address']) ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="cellphone_number" class="form-label">Cellphone Number</label>
                                                            <input type="text" name="cellphone_number" class="form-control" value="<?= htmlspecialchars($user['cellphone_number']) ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="phone_number" class="form-label">Phone Number</label>
                                                            <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($user['phone_number']) ?>">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="email" class="form-label">Email</label>
                                                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                                                        </div>
                                                        <button type="submit" name="updateUser" class="btn btn-primary">Save Changes</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </div>
</body>

</html>