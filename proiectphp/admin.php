<?php
session_start();
include("connection.php");
include("functions.php");

$user_data = check_login($con);

if (!isset($user_data['is_admin']) || $user_data['is_admin'] != 1) {
    die("Access denied. Admins only.");
}

if (isset($_POST['add_user'])) {
    $user_id = mysqli_real_escape_string($con, $_POST['user_id']);
    $user_name = mysqli_real_escape_string($con, $_POST['user_name']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $query = "INSERT INTO users (user_id, user_name, password, is_admin) VALUES ('$user_id', '$user_name', '$password', '$is_admin')";
    mysqli_query($con, $query);
    header("Location: admin.php");
    exit();
}

if (isset($_POST['edit_user'])) {
    $id = $_POST['id'];
    $user_id = mysqli_real_escape_string($con, $_POST['user_id']);
    $user_name = mysqli_real_escape_string($con, $_POST['user_name']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $query = "UPDATE users SET user_id='$user_id', user_name='$user_name', password='$password', is_admin='$is_admin' WHERE id='$id'";
    mysqli_query($con, $query);
    header("Location: admin.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $query = "DELETE FROM users WHERE id='$id'";
    mysqli_query($con, $query);
    header("Location: admin.php");
    exit();
}

$query = "SELECT * FROM users";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .container {
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            padding: 30px;
            width: 90%;
            max-width: 800px;
            text-align: center;
        }

        h1 {
            font-size: 26px;
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            color: #4a90e2; 
        }

        table th {
            background-color: #f4f4f9;
            color: #333; 
        }

        .actions a {
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            margin-right: 5px;
            transition: background-color 0.3s ease;
        }

        .edit {
            background-color: #5cb85c;
            color: white;
        }

        .edit:hover {
            background-color: #4cae4c;
        }

        .delete {
            background-color: #d9534f;
            color: white;
        }

        .delete:hover {
            background-color: #c9302c;
        }

        form {
            margin-top: 20px;
            text-align: left;
        }

        form input, form select, form button, form label {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 12px;
            font-size: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #f9f9f9;
        }

        form label {
            color: grey; 
            font-size: 15px; 
            margin-bottom: 8px; 
        }

        form input[type="checkbox"] {
            margin-right: 10px;
        }

        form button {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        form button:hover {
            background: linear-gradient(135deg, #2575fc, #6a11cb);
        }

        .task-list h2 {
            font-size: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Panel</h1>

        <form method="POST" action="admin.php">
            <input type="hidden" name="id" value="<?php echo isset($_GET['edit_id']) ? $_GET['edit_id'] : ''; ?>">
            <input type="text" name="user_id" placeholder="User ID" required value="<?php echo isset($_GET['edit_user_id']) ? $_GET['edit_user_id'] : ''; ?>">
            <input type="text" name="user_name" placeholder="Username" required value="<?php echo isset($_GET['edit_user_name']) ? $_GET['edit_user_name'] : ''; ?>">
            <input type="password" name="password" placeholder="Password" required value="<?php echo isset($_GET['edit_password']) ? $_GET['edit_password'] : ''; ?>">
            <label>
                <input type="checkbox" name="is_admin" <?php echo isset($_GET['edit_is_admin']) && $_GET['edit_is_admin'] == 1 ? 'checked' : ''; ?>> Is Admin
            </label>
            <?php if (isset($_GET['edit_id'])): ?>
                <button type="submit" name="edit_user">Save Changes</button>
            <?php else: ?>
                <button type="submit" name="add_user">Add User</button>
            <?php endif; ?>
        </form>

        <h2>User List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Admin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo $row['user_name']; ?></td>
                        <td><?php echo $row['password']; ?></td>
                        <td><?php echo $row['is_admin'] == 1 ? 'Yes' : 'No'; ?></td>
                        <td class="actions">
                            <a href="admin.php?edit_id=<?php echo $row['id']; ?>&edit_user_id=<?php echo $row['user_id']; ?>&edit_user_name=<?php echo $row['user_name']; ?>&edit_password=<?php echo $row['password']; ?>&edit_is_admin=<?php echo $row['is_admin']; ?>" class="edit">Edit</a>
                            <a href="admin.php?delete_id=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
