<?php
session_start();
require_once __DIR__ . '/../Utilities/tailwind_classes.php';
require_once __DIR__ . '/../Classes/Attendance.php';
require_once __DIR__ . '/../Classes/Student.php';
require_once __DIR__ . '/../Classes/Course.php';
require_once __DIR__ . '/../Classes/ExcuseLetter.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../../auth/login.php");
    exit();
}

$student = new Student();
$attendance = new Attendance();
$course = new Course();
$excuse = new ExcuseLetter();

$studentData = $student->getStudentWithCourse($_SESSION['student_id']);
$attendanceHistory = $attendance->getStudentAttendanceHistory($_SESSION['student_id']);
$excuseHistory = $excuse->getByStudent($_SESSION['student_id']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit_excuse') {
    try {
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['body'] ?? '');

        if ($body === '') {
            $error = 'Please provide the body of your excuse letter.';
        } else {
            $excuse->create([
                'student_id' => (int) $_SESSION['student_id'],
                'course_id' => (int) $studentData['course_id'],
                'subject' => $subject !== '' ? $subject : null,
                'body' => $body,
                'attachment_path' => null,
                'status' => 'pending'
            ]);
            $success = 'Excuse letter submitted. Awaiting admin review.';
            $excuseHistory = $excuse->getByStudent($_SESSION['student_id']);
        }
    } catch (Exception $e) {
        $error = 'Failed to submit excuse letter: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excuse Letter</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="<?= $body2 ?>">
    <!-- Header -->
    <header class="<?= $navbar ?>">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <h1 class="text-2xl font-bold text-gray-900">Excuse Letter</h1>
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

        <div class="<?= $contentCard ?>">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Compose an Excuse Letter</h2>

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
                <input type="hidden" name="action" value="submit_excuse">
                <div>
                    <label class="<?= $label ?> text-sm">Subject (optional)</label>
                    <input type="text" name="subject" class="<?= $formInput ?>">
                </div>
                <div>
                    <label class="<?= $label ?> text-sm">Body</label>
                    <textarea name="body" class="w-full border border-gray-200 bg-gray-50 h-32 font-mono p-2"
                        required></textarea>
                </div>
                <div>
                    <button class="<?= $button ?>">Submit this request for approval <i
                            class="fa-solid fa-paper-plane"></i></button>
                </div>
            </form>
        </div>

        <div class="<?= $contentCard ?>">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Your Excuse Letters</h2>
            <?php if (empty($excuseHistory)): ?>
                <p class="text-gray-500">No excuse letters submitted yet.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Admin Comment</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($excuseHistory as $letter): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= date('F j, Y g:i A', strtotime($letter['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($letter['subject'] ?? '(No subject)') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($letter['course_name'] . ' ' . $letter['year_level'] . ($letter['section'] ? '-' . $letter['section'] : '')) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    <?= $letter['status'] === 'approved' ? 'bg-green-100 text-green-800' : ($letter['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                            <?= ucfirst($letter['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($letter['admin_comment'] ?? '-') ?>
                                    </td>
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