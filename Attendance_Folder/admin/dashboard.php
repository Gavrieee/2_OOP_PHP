<?php
session_start();
require_once __DIR__ . '/../Utilities/tailwind_classes.php';
require_once __DIR__ . '/../Classes/Attendance.php';
require_once __DIR__ . '/../Classes/Student.php';
require_once __DIR__ . '/../Classes/Course.php';
require_once __DIR__ . '/../Classes/User.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

$attendance = new Attendance();
$student = new Student();
$course = new Course();
$user = new User();

$courses = $course->getAllCoursesWithDetails();
$attendanceSummary = $attendance->getAttendanceSummary();

$error = '';
$success = '';

// Handle course creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {

    $courseName = trim($_POST['course_name'] ?? '');
    $yearLevel = trim($_POST['year_level'] ?? '');
    $section = trim($_POST['section'] ?? '');

    if (empty($courseName) || empty($yearLevel)) {
        $error = 'Course name and year level are required';
    } else {
        try {
            $courseData = [
                'course_name' => $courseName,
                'year_level' => $yearLevel,
                'section' => $section
            ];

            $course->create($courseData);
            $success = 'Course added successfully!';

            // Refresh courses list
            $courses = $course->getAllCoursesWithDetails();
        } catch (Exception $e) {
            $error = 'Failed to add course: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="<?= $body2 ?>">
    <!-- Header -->
    <header class="<?= $navbar ?>">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                    <a href="../../auth/logout.php"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Students</p>
                        <p class="text-2xl font-semibold text-gray-900">
                            <?= array_sum(array_column($courses, 'student_count')) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-graduation-cap text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Courses</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= count($courses) ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Today's Date</p>
                        <p class="text-lg font-semibold text-gray-900"><?= date('M j, Y') ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Current Time</p>
                        <p class="text-lg font-semibold text-gray-900"><?= date('g:i A') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Course Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Add New Course</h2>

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

            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="course_name" class="<?= $label ?>">Course Name</label>
                        <input type="text" id="course_name" name="course_name" required class="
                                <?= $formInput ?>">
                    </div>
                    <div>
                        <label for="year_level" class="<?= $label ?>">Year Level</label>
                        <input type="text" id="year_level" name="year_level" placeholder="e.g., 4-1" required
                            class="<?= $formInput ?>">
                    </div>
                    <div>
                        <label for="section" class="<?= $label ?>">Section
                            (Optional)</label>
                        <input type="text" id="section" name="section" placeholder="e.g., A, B, C" class="
                                <?= $formInput ?>">
                    </div>
                </div>
                <button type="submit" name="add_course"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md font-medium">
                    Add Course
                </button>
            </form>
        </div>

        <!-- Course Management -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="mb-4 flex flex-row justify-between">
                <h2 class="text-xl font-semibold text-gray-900 ">Course Management</h2>
                <a href="letters.php" class="text-sm text-blue-500 hover:text-blue-600">View excuse letters</a>
            </div>


            <?php if (empty($courses)): ?>
                <p class="text-gray-500 text-center py-8">No courses found.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-center">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Course</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Year Level</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Section</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Students</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($courses as $courseData): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($courseData['course_name']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($courseData['year_level']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($courseData['section'] ?? '-') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= $courseData['student_count'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="view_attendance.php?course_id=<?= $courseData['course_id'] ?>"
                                            class="text-blue-600 hover:text-blue-900 mr-3">View Attendance</a>
                                        <a href="edit_course.php?id=<?= $courseData['course_id'] ?>"
                                            class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Attendance Check -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Attendance Check</h2>

            <form method="GET" action="view_attendance.php" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="course_name_filter" class="<?= $label ?>">Course</label>
                        <select id="course_name_filter" name="course_name" required class="<?= $formInput ?>"
                            onchange="updateYearLevels()">
                            <option value="">Select Course</option>
                            <?php foreach (array_unique(array_column($courses, 'course_name')) as $courseName): ?>
                                <option value="<?= htmlspecialchars($courseName) ?>">
                                    <?= htmlspecialchars($courseName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="year_level_filter" class="<?= $label ?>">Year Level</label>
                        <select id="year_level_filter" name="year_level" required class="<?= $formInput ?>"
                            onchange="updateSections()">
                            <option value="">Select Year Level</option>
                        </select>
                    </div>
                    <div>
                        <label for="section_filter" class="<?= $label ?>">Section</label>
                        <select id="section_filter" name="section" class="<?= $formInput ?>">
                            <option value="">All Sections</option>
                        </select>
                    </div>
                    <div>
                        <label for="date_filter" class="<?= $label ?>">Date</label>
                        <input type="date" id="date_filter" name="date" value="<?= date('Y-m-d') ?>"
                            class="<?= $formInput ?>">
                    </div>
                </div>
                <button type="submit"
                    class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md font-medium">
                    Check Attendance
                </button>
            </form>
        </div>

        <script>
            // Store all courses data for filtering
            const allCourses = <?= json_encode($courses) ?>;

            function updateYearLevels() {
                const courseSelect = document.getElementById('course_name_filter');
                const yearLevelSelect = document.getElementById('year_level_filter');
                const sectionSelect = document.getElementById('section_filter');

                const selectedCourse = courseSelect.value;

                // Reset year level and section
                yearLevelSelect.innerHTML = '<option value="">Select Year Level</option>';
                sectionSelect.innerHTML = '<option value="">All Sections</option>';

                if (selectedCourse) {
                    // Get unique year levels for selected course
                    const yearLevels = [...new Set(
                        allCourses
                            .filter(course => course.course_name === selectedCourse)
                            .map(course => course.year_level)
                    )];

                    yearLevels.forEach(yearLevel => {
                        const option = document.createElement('option');
                        option.value = yearLevel;
                        option.textContent = yearLevel;
                        yearLevelSelect.appendChild(option);
                    });
                }
            }

            function updateSections() {
                const courseSelect = document.getElementById('course_name_filter');
                const yearLevelSelect = document.getElementById('year_level_filter');
                const sectionSelect = document.getElementById('section_filter');

                const selectedCourse = courseSelect.value;
                const selectedYearLevel = yearLevelSelect.value;

                // Reset section
                sectionSelect.innerHTML = '<option value="">All Sections</option>';

                if (selectedCourse && selectedYearLevel) {
                    // Get unique sections for selected course and year level
                    const sections = [...new Set(
                        allCourses
                            .filter(course => course.course_name === selectedCourse && course.year_level ===
                                selectedYearLevel)
                            .map(course => course.section)
                            .filter(section => section) // Filter out null/empty sections
                    )];

                    sections.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section;
                        option.textContent = section;
                        sectionSelect.appendChild(option);
                    });
                }
            }
        </script>
    </div>
</body>

</html>