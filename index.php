<?php
session_start();
require_once __DIR__ . '/Attendance_Folder/Utilities/tailwind_classes.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'student') {
        header("Location: Attendance_Folder/student/dashboard.php");
        exit();
    } else {
        header("Location: Attendance_Folder/admin/dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management System</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="<?= $body2 ?>">
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-[5%] rounded-2xl border border-blue-300">
        <div class="max-w-4xl mx-auto text-center px-4">
            <!-- Header -->
            <div class="mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-600 rounded-full mb-6">
                    <i class="fas fa-graduation-cap text-white text-3xl"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Attendance Management System</h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    A comprehensive system for managing student attendance with role-based access control.
                    Track attendance, manage courses, and generate reports efficiently.
                </p>
            </div>

            <!-- Features -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Student Portal</h3>
                    <p class="text-gray-600">Submit daily attendance and view your attendance history with late status
                        tracking.</p>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-shield text-green-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Admin Portal</h3>
                    <p class="text-gray-600">Manage courses, view attendance reports, and monitor student performance.
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-bar text-purple-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Analytics</h3>
                    <p class="text-gray-600">Comprehensive attendance analytics with late arrival tracking and
                        reporting.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="auth/login.php"
                        class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Login
                    </a>
                    <a href="auth/register.php"
                        class="inline-flex items-center justify-center px-8 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>
                        Register
                    </a>
                </div>

                <p class="text-sm text-gray-500">
                    Default admin account: <strong>admin</strong> / <strong>admin123</strong>
                </p>
            </div>

            <!-- System Info -->
            <div class="mt-16 bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">System Features</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">For Students:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Daily attendance submission</li>
                            <li>• Attendance history viewing</li>
                            <li>• Late status tracking</li>
                            <li>• Course and year level identification</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">For Admins:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Course and program management</li>
                            <li>• Attendance monitoring per course/year</li>
                            <li>• Student registration management</li>
                            <li>• Comprehensive reporting</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>