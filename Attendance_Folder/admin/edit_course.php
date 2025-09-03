<?php
session_start();
require_once __DIR__ . '/../Utilities/tailwind_classes.php';
require_once __DIR__ . '/../Classes/Course.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

$course = new Course();
$error = '';
$success = '';

$courseId = $_GET['id'] ?? null;
if (!$courseId) {
    header("Location: dashboard.php");
    exit();
}

$courseData = $course->getCourseById($courseId);
if (!$courseData) {
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseName = trim($_POST['course_name'] ?? '');
    $yearLevel = trim($_POST['year_level'] ?? '');
    $section = trim($_POST['section'] ?? '');

    if (empty($courseName) || empty($yearLevel)) {
        $error = 'Course name and year level are required';
    } else {
        try {
            $updateData = [
                'course_name' => $courseName,
                'year_level' => $yearLevel,
                'section' => $section
            ];

            $course->update($courseId, $updateData);
            $success = 'Course updated successfully!';

            // Refresh course data
            $courseData = $course->getCourseById($courseId);
        } catch (Exception $e) {
            $error = 'Failed to update course: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course - Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="<?= $body2 ?>">
    <div class="min-h-screen bg-gray-100">
        <!-- Header -->
        <header class="<?= $navbar ?>">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <h1 class="text-2xl font-bold text-gray-900">Edit Course</h1>
                    <div class="flex items-center space-x-4">
                        <a href="dashboard.php" class="<?= $backButton ?>">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                        </a>
                        <a href="../../auth/logout.php"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium">Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Edit Course Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Edit Course:
                    <?= htmlspecialchars($courseData['course_name'] . ' ' . $courseData['year_level']) ?>
                </h2>

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

                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="course_name" class="<?= $label ?>">Course Name</label>
                            <input type="text" id="course_name" name="course_name"
                                value="<?= htmlspecialchars($courseData['course_name']) ?>" required
                                class="<?= $formInput ?>">
                        </div>
                        <div>
                            <label for="year_level" class="<?= $label ?>">Year Level</label>
                            <input type="text" id="year_level" name="year_level"
                                value="<?= htmlspecialchars($courseData['year_level']) ?>" placeholder="e.g., 4-1"
                                required class="<?= $formInput ?>">
                        </div>
                        <div>
                            <label for="section" class="<?= $label ?>">Section
                                (Optional)</label>
                            <input type="text" id="section" name="section"
                                value="<?= htmlspecialchars($courseData['section'] ?? '') ?>"
                                placeholder="e.g., A, B, C" class="<?= $formInput ?>">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="dashboard.php"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md font-medium">
                            Cancel
                        </a>
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md font-medium">
                            Update Course
                        </button>
                    </div>
                </form>
            </div>

            <!-- Course Information -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="<?= $label ?>">Course ID</label>
                        <p class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($courseData['course_id']) ?></p>
                    </div>
                    <div>
                        <label class="<?= $label ?>">Created Date</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <?= date('F j, Y', strtotime($courseData['created_at'])) ?>
                        </p>
                    </div>
                    <div>
                        <label class="<?= $label ?>">Last Updated</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <?= date('F j, Y', strtotime($courseData['updated_at'])) ?>
                        </p>
                    </div>
                    <div>
                        <label class="<?= $label ?>">Actions</label>
                        <div class="mt-1 space-x-2">
                            <a href="view_attendance.php?course_id=<?= $courseData['course_id'] ?>"
                                class="text-blue-600 hover:text-blue-900 text-sm">View Attendance</a>
                            <span class="text-gray-400">|</span>
                            <a href="dashboard.php" class="text-gray-600 hover:text-gray-900 text-sm">Back to
                                Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>