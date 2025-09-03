<?php
session_start();
require_once __DIR__ . '/../Attendance_Folder/Utilities/tailwind_classes.php';
require_once __DIR__ . '/../Attendance_Folder/Classes/User.php';
require_once __DIR__ . '/../Attendance_Folder/Classes/Student.php';
require_once __DIR__ . '/../Attendance_Folder/Classes/Course.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validation
    if (empty($username) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } else {
        try {
            $user = new User();

            // Check if username already exists
            if ($user->findByUsername($username)) {
                $error = 'Username already exists';
            } else {
                // Create user
                $userData = [
                    'username' => $username,
                    'password' => $password,
                    'role' => $role
                ];

                $user->create($userData);
                $userId = $user->getPDO()->lastInsertId();

                // If student, create student record
                if ($role === 'student') {
                    $first_name = trim($_POST['first_name'] ?? '');
                    $last_name = trim($_POST['last_name'] ?? '');
                    $course_id = $_POST['course'] ?? '';

                    if (empty($first_name) || empty($last_name) || empty($course_id)) {
                        $error = 'Student information is required';
                    } else {
                        $student = new Student();
                        $studentData = [
                            'user_id' => $userId,
                            'course_id' => $course_id,
                            'first_name' => $first_name,
                            'last_name' => $last_name
                        ];

                        $student->create($studentData);
                        $success = 'Student registered successfully! You can now log in.';
                    }
                } else {
                    $success = 'Admin registered successfully! You can now log in.';
                }

                if ($success) {
                    header("Location: login.php?success=" . urlencode($success));
                    exit();
                }
            }
        } catch (Exception $e) {
            $error = 'Registration failed: ' . $e->getMessage();
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
        <h2 class="<?= $h2 ?>">Register</h2>

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
        <form action="register.php" method="POST" class="space-y-4">
            <div>
                <label for="role" class="<?= $label ?>">Register as</label>
                <select id="role" name="role" class="<?= $formInput ?>" required onchange="toggleCourseField()">
                    <option value="" disabled selected>Select your role</option>
                    <option value="student">Student</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div>
                <label for="username" class="<?= $label ?>">Username</label>
                <input type="text" id="username" name="username" class="<?= $formInput ?>" required>
            </div>
            <div>
                <label for="password" class="<?= $label ?>">Password</label>
                <input type="password" id="password" name="password" class="<?= $formInput ?>" required>
            </div>
            <div>
                <label for="confirm_password" class="<?= $label ?>">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="<?= $formInput ?>" required>
            </div>
            <div id="studentFields" class="space-y-4">
                <div class="flex flex-row gap-4">
                    <div class="flex-1">
                        <label for="first_name" class="<?= $label ?>">First Name</label>
                        <input type="text" id="first_name" name="first_name" class="<?= $formInput ?>">
                    </div>
                    <div class="flex-1">
                        <label for="last_name" class="<?= $label ?>">Last Name</label>
                        <input type="text" id="last_name" name="last_name" class="<?= $formInput ?>">
                    </div>
                </div>

                <div>
                    <label for="course" class="<?= $label ?>">Course and Year Level</label>
                    <select id="course" name="course" class="<?= $formInput ?>">
                        <option value="" disabled selected>Select your course & year level</option>
                        <option value="1">UCOS 4-1</option>
                        <option value="2">UCOS 4-2</option>
                        <option value="3">UCOS 4-3</option>
                        <option value="4">UCOS 4-4</option>
                    </select>
                </div>
            </div>
            <div>
                <button type="submit" class="<?= $button ?>">Register</button>
            </div>
        </form>
        <script>
        function toggleCourseField() {
            const role = document.getElementById('role').value;
            const studentFields = document.getElementById('studentFields');
            const courseSelect = document.getElementById('course');

            if (role === 'student') {
                studentFields.style.display = 'block';
                courseSelect.required = true;
                document.getElementById('first_name').required = true;
                document.getElementById('last_name').required = true;
            } else {
                studentFields.style.display = 'none';
                courseSelect.required = false;
                document.getElementById('first_name').required = false;
                document.getElementById('last_name').required = false;
            }
        }
        </script>
        <div class="flex justify-center mt-4">
            Already have an account? <a href="login.php" class="text-blue-500 hover:underline pl-1"> Log in here</a>.
        </div>
    </div>

</body>

</html>