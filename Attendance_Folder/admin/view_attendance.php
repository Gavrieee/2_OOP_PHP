<?php
session_start();
require_once __DIR__ . '/../Utilities/tailwind_classes.php';
require_once __DIR__ . '/../Classes/Attendance.php';
require_once __DIR__ . '/../Classes/Student.php';
require_once __DIR__ . '/../Classes/Course.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

$attendance = new Attendance();
$student = new Student();
$course = new Course();

$courseId = $_GET['course_id'] ?? null;
$courseName = $_GET['course_name'] ?? '';
$yearLevel = $_GET['year_level'] ?? '';
$section = $_GET['section'] ?? '';
$date = $_GET['date'] ?? date('Y-m-d');

$attendanceRecords = [];
$courseData = null;
$students = [];

if ($courseId) {
    // View attendance by specific course
    $courseData = $course->getCourseById($courseId);
    $attendanceRecords = $attendance->getAttendanceByCourseAndDate($courseId, $date);
    $students = $student->getStudentsByCourse($courseId);
} elseif ($courseName && $yearLevel) {
    // View attendance by course name, year level, and optionally section
    $whereConditions = ['course_name' => $courseName, 'year_level' => $yearLevel];
    if (!empty($section)) {
        $whereConditions['section'] = $section;
    }

    $courses = $course->findAll('courses', $whereConditions);
    if (!empty($courses)) {
        $courseData = $courses[0];
        $students = $student->getStudentsByCourse($courseData['course_id']);

        // Get attendance records for the specific course
        if (!empty($section)) {
            // Filter by course, year level, and section
            $attendanceRecords = $attendance->getAttendanceByCourseAndYearLevelAndSection($courseName, $yearLevel, $section, $date);
        } else {
            // Filter by course and year level only
            $attendanceRecords = $attendance->getAttendanceByCourseAndYearLevel($courseName, $yearLevel, $date);
        }
    }
}

// Get all students for this course to show absent ones
$allStudents = [];
if ($courseData) {
    $allStudents = $student->getStudentsByCourse($courseData['course_id']);
}

// Create a map of present students
$presentStudents = [];
foreach ($attendanceRecords as $record) {
    $presentStudents[$record['student_id']] = $record;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance - Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="<?= $body2 ?>">
    <div class="min-h-screen bg-gray-100">
        <!-- Header -->
        <header class="<?= $navbar ?>">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <h1 class="text-2xl font-bold text-gray-900">View Attendance</h1>
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

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Course Information -->
            <?php if ($courseData): ?>
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Course Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="<?= $label ?>">Course Name</label>
                        <p class="<?= $formInput ?>"><?= htmlspecialchars($courseData['course_name']) ?></p>
                    </div>
                    <div>
                        <label class="<?= $label ?>">Year Level</label>
                        <p class="<?= $formInput ?>"><?= htmlspecialchars($courseData['year_level']) ?></p>
                    </div>
                    <div>
                        <label class="<?= $label ?>">Section</label>
                        <p class="<?= $formInput ?>"><?= htmlspecialchars($courseData['section'] ?? '-') ?></p>
                    </div>
                    <div>
                        <label class="<?= $label ?>">Total Students</label>
                        <p class="<?= $formInput ?>"><?= count($allStudents) ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Date Filter -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter by Date</h3>
                <form method="GET" class="flex items-end space-x-4">
                    <?php if ($courseId): ?>
                    <input type="hidden" name="course_id" value="<?= htmlspecialchars($courseId) ?>">
                    <?php endif; ?>
                    <?php if ($courseName): ?>
                    <input type="hidden" name="course_name" value="<?= htmlspecialchars($courseName) ?>">
                    <?php endif; ?>
                    <?php if ($yearLevel): ?>
                    <input type="hidden" name="year_level" value="<?= htmlspecialchars($yearLevel) ?>">
                    <?php endif; ?>
                    <?php if ($section): ?>
                    <input type="hidden" name="section" value="<?= htmlspecialchars($section) ?>">
                    <?php endif; ?>

                    <div>
                        <label for="date" class="<?= $label ?>">Date</label>
                        <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>"
                            class="<?= $formInput ?>">
                    </div>
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md font-medium">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Attendance Summary -->
            <?php if ($courseData): ?>
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendance Summary for
                    <?= date('F j, Y', strtotime($date)) ?>
                </h3>

                <?php
                    $presentCount = count($presentStudents);
                    $lateCount = 0;
                    foreach ($presentStudents as $student) {
                        if ($student['is_late']) {
                            $lateCount++;
                        }
                    }
                    $absentCount = count($allStudents) - $presentCount;
                    ?>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-green-800">Present</p>
                                <p class="text-2xl font-bold text-green-900"><?= $presentCount ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-yellow-500 text-xl mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-yellow-800">Late</p>
                                <p class="text-2xl font-bold text-yellow-900"><?= $lateCount ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-times-circle text-red-500 text-xl mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-red-800">Absent</p>
                                <p class="text-2xl font-bold text-red-900"><?= $absentCount ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-percentage text-blue-500 text-xl mr-3"></i>
                            <div>
                                <p class="text-sm font-medium text-blue-800">Attendance Rate</p>
                                <p class="text-2xl font-bold text-blue-900">
                                    <?= count($allStudents) > 0 ? round(($presentCount / count($allStudents)) * 100, 1) : 0 ?>%
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Attendance Records -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Student Attendance Records</h3>

                <?php if (empty($allStudents)): ?>
                <p class="text-gray-500 text-center py-8">No students found for this course.</p>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Student Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Time In</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Late</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($allStudents as $student): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (isset($presentStudents[$student['student_id']])): ?>
                                    <?php if ($presentStudents[$student['student_id']]['is_late']): ?>
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Late
                                    </span>
                                    <?php else: ?>
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Present
                                    </span>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Absent
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if (isset($presentStudents[$student['student_id']])): ?>
                                    <?= date('g:i A', strtotime($presentStudents[$student['student_id']]['time_in'])) ?>
                                    <?php else: ?>
                                    -
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if (isset($presentStudents[$student['student_id']]) && $presentStudents[$student['student_id']]['is_late']): ?>
                                    <span class="text-yellow-600 font-medium">Yes</span>
                                    <?php else: ?>
                                    <span class="text-gray-500">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if (isset($presentStudents[$student['student_id']])): ?>
                                    <?php if ($presentStudents[$student['student_id']]['is_late']): ?>
                                    <span class="text-yellow-600">Arrived after 8:00 AM</span>
                                    <?php else: ?>
                                    <span class="text-green-600">On time</span>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <span class="text-red-600">No attendance recorded</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>