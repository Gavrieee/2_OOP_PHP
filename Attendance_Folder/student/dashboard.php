<?php
session_start();
require_once __DIR__ . '/../Utilities/tailwind_classes.php';
require_once __DIR__ . '/../Classes/Attendance.php';
require_once __DIR__ . '/../Classes/Student.php';
require_once __DIR__ . '/../Classes/Course.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../auth/login.php");
    exit();
}

$student = new Student();
$attendance = new Attendance();
$course = new Course();

$studentData = $student->getStudentWithCourse($_SESSION['student_id']);
$attendanceHistory = $attendance->getStudentAttendanceHistory($_SESSION['student_id']);

$error = '';
$success = '';

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_attendance'])) {
    try {
        $attendanceData = [
            'student_id' => $_SESSION['student_id'],
            'date' => date('Y-m-d'),
            'time_in' => date('H:i:s')
        ];

        $attendance->create($attendanceData);
        $success = 'Attendance recorded successfully!';

        // Refresh attendance history
        $attendanceHistory = $attendance->getStudentAttendanceHistory($_SESSION['student_id']);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Check if attendance already submitted today
$todayAttendance = $attendance->getAttendanceByStudentAndDate($_SESSION['student_id'], date('Y-m-d'));
$canSubmitAttendance = !$todayAttendance;

$sectionMap = array(
    'A' => 1,
    'B' => 2,
    'C' => 3,
    'D' => 4
);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="<?= $body2 ?>">
    <!-- Header -->
    <header class="<?= $navbar ?>">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 ">
            <div class="flex justify-between items-center py-4 space-x-4">
                <h1 class="text-2xl font-bold text-gray-900">Student Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, <?= htmlspecialchars($_SESSION['student_name']) ?></span>
                    <a href="../../auth/logout.php"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl max-h-screen mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Student Info Card -->
        <div class="<?= $contentCard ?>">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Student Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="<?= $label ?>">Name</label>
                    <p class="mt-1 text-sm text-gray-900">
                        <?= htmlspecialchars($studentData['first_name'] . ' ' . $studentData['last_name']) ?>
                    </p>
                </div>
                <div>
                    <label class="<?= $label ?>">Course</label>
                    <p class="mt-1 text-sm text-gray-900">
                        <?= htmlspecialchars($studentData['course_name'] . ' ' . $studentData['year_level']) ?>
                    </p>
                </div>
                <div>
                    <label class="<?= $label ?>">Section</label>
                    <p class="mt-1 text-sm text-gray-900">
                        <?= htmlspecialchars($studentData['section']); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Attendance Submission -->
        <?php if ($canSubmitAttendance): ?>
            <div class="<?= $contentCard ?>">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Submit Attendance</h2>

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
                    <input type="hidden" name="action" value="create">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="<?= $label ?>">Date</label>
                            <input type="date" value="<?= date('Y-m-d') ?>" class="<?= $formInput ?>" readonly>
                        </div>
                        <div>
                            <label class="<?= $label ?>">Time</label>
                            <input type="time" value="<?= date('H:i:s') ?>" class="<?= $formInput ?>" readonly>
                        </div>
                    </div>
                    <button type="submit" name="submit_attendance"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md font-medium">
                        Submit Attendance
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-lg font-medium text-green-800">Attendance Already Submitted</h3>
                        <p class="text-green-700">You have already submitted your attendance for today
                            (<?= date('F j, Y') ?>).</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Attendance History -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-4 flex flex-row justify-between">
                <h2 class="text-xl font-semibold text-gray-900 ">Attendance History</h2>
                <a href="letter_request.php" class="text-sm text-blue-500 hover:text-blue-600">Request an excuse
                    letter</a>
            </div>


            <?php if (empty($attendanceHistory)): ?>
                <p class="text-gray-500 text-center py-8">No attendance records found.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Time In</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <!-- <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Late</th> -->
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($attendanceHistory as $record): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= date('F j, Y', strtotime($record['date'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= date('g:i A', strtotime($record['time_in'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        <?= $record['status'] === 'present' ? 'bg-green-100 text-green-800' :
                                            ($record['status'] === 'late' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                            <?= ucfirst($record['status']) ?>
                                        </span>
                                    </td>
                                    <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($record['is_late']): ?>
                                    <span class="text-yellow-600 font-medium">Yes</span>
                                    <?php else: ?>
                                    <span class="text-green-600 font-medium">No</span>
                                    <?php endif; ?>
                                </td> -->
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>