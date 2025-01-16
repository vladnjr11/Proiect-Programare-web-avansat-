<?php
session_start();
include("connection.php");
include("functions.php");

$user_data = check_login($con);

if (!$user_data) {
    header("Location: login.php");
    die;
}

$is_admin = isset($user_data['is_admin']) && $user_data['is_admin'] == 1 ? true : false;

// Adăugare task
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_task'])) {
    $task_name = $_POST['task_name'];
    $task_description = $_POST['task_description'];
    $deadline = $_POST['deadline']; // Data limită
    $user_id = $user_data['id'];

    if (!empty($task_name)) {
        $query = "INSERT INTO tasks (user_id, task_name, task_description, deadline) VALUES ('$user_id', '$task_name', '$task_description', '$deadline')";
        mysqli_query($con, $query);
        echo "<script>alert('Task added successfully!');</script>";
    } else {
        echo "<script>alert('Task name is required.');</script>";
    }
}

// Ștergere task
if (isset($_POST['delete_task'])) {
    $task_id = $_POST['task_id'];
    $user_id = $user_data['id'];

    $query = "DELETE FROM tasks WHERE id = '$task_id' AND user_id = '$user_id'";
    $result = mysqli_query($con, $query);

    if ($result) {
        echo "<script>alert('Task deleted successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error deleting task');</script>";
    }
}

// Marcarea unui task ca finalizat
if (isset($_POST['mark_completed'])) {
    $task_id = $_POST['task_id'];
    $current_date = date('Y-m-d'); // Data curentă

    $query = "SELECT deadline FROM tasks WHERE id = '$task_id'";
    $result = mysqli_query($con, $query);
    $task = mysqli_fetch_assoc($result);

    if ($task) {
        $deadline = $task['deadline'];

        if ($current_date <= $deadline) {
            $on_time = 1; // La timp
            $is_delayed = 0;
        } else {
            $on_time = 0; // Cu întârziere
            $is_delayed = 1;
        }

        $update_query = "UPDATE tasks SET is_completed = 1, on_time = '$on_time', is_delayed = '$is_delayed' WHERE id = '$task_id'";
        mysqli_query($con, $update_query);
        echo "<script>alert('Task marked as completed!');</script>";
    }
}

// Obținerea task-urilor
$user_id = $user_data['id'];

// Task-uri nefinalizate
$query_pending = "SELECT * FROM tasks WHERE user_id = '$user_id' AND is_completed = 0";
$result_pending = mysqli_query($con, $query_pending);
$tasks_pending = mysqli_fetch_all($result_pending, MYSQLI_ASSOC);

// Task-uri completate la timp
$query_completed_on_time = "SELECT * FROM tasks WHERE user_id = '$user_id' AND is_completed = 1 AND on_time = 1";
$result_on_time = mysqli_query($con, $query_completed_on_time);
$tasks_on_time = mysqli_fetch_all($result_on_time, MYSQLI_ASSOC);

// Task-uri completate cu întârziere
$query_completed_late = "SELECT * FROM tasks WHERE user_id = '$user_id' AND is_completed = 1 AND is_delayed = 1";
$result_late = mysqli_query($con, $query_completed_late);
$tasks_late = mysqli_fetch_all($result_late, MYSQLI_ASSOC);

// Comparația numărului de task-uri completate la timp și cu întârziere
$on_time_count = count($tasks_on_time);
$late_count = count($tasks_late);

// Debug pentru variabile
//echo "<pre>";
//echo "On Time Count: $on_time_count\n";
//echo "Late Count: $late_count\n";
//echo "</pre>";

$message = '';
if ($on_time_count > $late_count) {
    $message = 'Felicitări! Ai terminat mai multe taskuri la timp!';
} elseif ($on_time_count < $late_count) {
    $message = 'Păcat! Ai terminat mai multe taskuri cu întârziere!';
} else {
    $message = 'Ai terminat același număr de taskuri la timp și cu întârziere.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        /* Stiluri generale */
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
            width: 800px;
            text-align: center;
            margin-top: 850px; /* Adăugăm margine de sus pentru a muta containerul mai jos */
        }

        .task-list {
            margin-top: 20px;
            display: inline-block;
            vertical-align: top;
            width: 45%;
            margin: 0 2%;
            text-align: left;
        }

        .task {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            color: #333;
        }

        .delete-btn, .complete-btn {
            width: 100%;
            padding: 5px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            margin-top: 10px;
        }

        .delete-btn {
            background-color: #d9534f;
            color: white;
        }

        .complete-btn {
            background-color: #5bc0de;
            color: white;
        }

        .add-task-form {
            margin-top: 20px;
            text-align: left;
        }

        .add-task-form label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: left;
        }

        .add-task-form input, .add-task-form textarea, .add-task-form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 6px;
            font-size: 14px;
            border: 1px solid #ddd;
        }

        .add-task-form button {
            background-color: #28a745;
            color: white;
            cursor: pointer;
        }

        .add-task-form input, .add-task-form textarea {
            margin-top: 5px;
        }

        /* Stil pentru mesajul de felicitări sau păcat */
        .message {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            padding: 10px;
            border-radius: 6px;
            background-color: #28a745; /* Verde pentru felicitări */
        }

        .message.pacat {
            background-color: #d9534f; /* Roșu pentru păcat */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($user_data['user_name']); ?>!</h1>

        <!-- Mesaj de felicitare sau păcat -->
        <div class="message <?php echo ($on_time_count < $late_count) ? 'pacat' : ''; ?>">
            <?php echo $message; ?>
        </div>

        <!-- Form pentru adăugarea unui task -->
        <div class="add-task-form">
            <h3>Add New Task</h3>
            <form method="POST">
                <label for="task_name">Task Name:</label>
                <input type="text" id="task_name" name="task_name" placeholder="Task Name" required>
                
                <label for="task_description">Task Description:</label>
                <textarea id="task_description" name="task_description" placeholder="Task Description" rows="4"></textarea>
                
                <label for="deadline">Deadline:</label>
                <input type="date" id="deadline" name="deadline" required>
                
                <button type="submit" name="add_task">Add Task</button>
            </form>
        </div>

        <!-- Task-uri nefinalizate -->
        <div class="task-list">
            <h2>Pending Tasks</h2>
            <?php if (!empty($tasks_pending)): ?>
                <?php foreach ($tasks_pending as $task): ?>
                    <div class="task">
                        <h3><?php echo htmlspecialchars($task['task_name']); ?></h3>
                        <p><?php echo htmlspecialchars($task['task_description']); ?></p>
                        <small>Deadline: <?php echo htmlspecialchars($task['deadline']); ?></small>
                        <form method="POST">
                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            <button type="submit" name="mark_completed" class="complete-btn">Mark as Completed</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No pending tasks.</p>
            <?php endif; ?>
        </div>

        <!-- Task-uri completate la timp -->
        <div class="task-list">
            <h2>Completed On Time</h2>
            <?php if (!empty($tasks_on_time)): ?>
                <?php foreach ($tasks_on_time as $task): ?>
                    <div class="task">
                        <h3><?php echo htmlspecialchars($task['task_name']); ?></h3>
                        <p><?php echo htmlspecialchars($task['task_description']); ?></p>
                        <small>Completed on time!</small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tasks completed on time.</p>
            <?php endif; ?>
        </div>

        <!-- Task-uri completate cu întârziere -->
        <div class="task-list">
            <h2>Completed Late</h2>
            <?php if (!empty($tasks_late)): ?>
                <?php foreach ($tasks_late as $task): ?>
                    <div class="task">
                        <h3><?php echo htmlspecialchars($task['task_name']); ?></h3>
                        <p><?php echo htmlspecialchars($task['task_description']); ?></p>
                        <small>Completed late!</small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No tasks completed late.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
