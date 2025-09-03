<?php
session_start();
require_once __DIR__ . '/../Attendance_Folder/Utilities/tailwind_classes.php';
require_once __DIR__ . '/../Attendance_Folder/Classes/User.php';
require_once __DIR__ . '/../Attendance_Folder/Classes/Student.php';

$error = '';
$success = '';

// Check for success message from registration
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username and password are required';
    } else {
        try {
            $user = new User();
            $userData = $user->authenticate($username, $password);

            if ($userData) {
                $_SESSION['user_id'] = $userData['user_id'];
                $_SESSION['username'] = $userData['username'];
                $_SESSION['role'] = $userData['role'];

                if ($userData['role'] === 'student') {
                    // Get student information
                    $student = new Student();
                    $studentData = $student->getStudentByUserId($userData['user_id']);
                    if ($studentData) {
                        $_SESSION['student_id'] = $studentData[0]['student_id'];
                        $_SESSION['student_name'] = $studentData[0]['first_name'] . ' ' . $studentData[0]['last_name'];
                    }
                    header("Location: ../Attendance_Folder/student/dashboard.php");
                } else {
                    header("Location: ../Attendance_Folder/admin/dashboard.php");
                }
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } catch (Exception $e) {
            $error = 'Login failed: ' . $e->getMessage();
        }
    }
}
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="<?= $body2 ?> p-[3%]">
    <div class="<?= $mainCard2 ?>">
        <h2 class="<?= $h2 ?>">Log in</h2>

        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>
        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label for="username" class="<?= $label ?>">Username:</label>
                <input type="text" id="username" name="username" class="<?= $formInput ?>" required>
            </div>
            <div>
                <label for="password" class="<?= $label ?>">Password:</label>
                <input type="password" id="password" name="password" class="<?= $formInput ?>" required>
            </div>
            <div>
                <button type="submit" class="<?= $button ?>">Enter</button>
            </div>
        </form>
        <div class="flex justify-center mt-4">
            Don't have an account yet? <a href="register.php" class="text-blue-500 hover:underline pl-1">Register
                here</a>.
        </div>
    </div>

</body>

</html>