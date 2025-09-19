<?php
session_start();
require_once __DIR__ . '/../Utilities/tailwind_classes.php';
require_once __DIR__ . '/../Classes/ExcuseLetter.php';
require_once __DIR__ . '/../Classes/Course.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

$letters = new ExcuseLetter();
$course = new Course();

$courses = $course->getAllCoursesWithDetails();

$error = '';
$success = '';

// Actions: approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['letter_id'])) {
    $action = $_POST['action'];
    $letterId = (int) $_POST['letter_id'];
    $comment = trim($_POST['admin_comment'] ?? '');

    try {
        if ($action === 'approve') {
            $letters->updateStatus($letterId, 'approved', $comment ?: null);
            $success = 'Letter approved.';
        } elseif ($action === 'reject') {
            $letters->updateStatus($letterId, 'rejected', $comment ?: null);
            $success = 'Letter rejected.';
        }
    } catch (Exception $e) {
        $error = 'Failed to update letter: ' . $e->getMessage();
    }
}

// Filters
$courseName = $_GET['course_name'] ?? '';
$yearLevel = $_GET['year_level'] ?? '';
$section = $_GET['section'] ?? '';
$status = $_GET['status'] ?? '';

$letterRows = $letters->getByFilters(
    $courseName ?: null,
    $yearLevel ?: null,
    $section ?: null,
    $status ?: null
);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excuse Letters - Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="<?= $body2 ?>">
    <header class="<?= $navbar ?>">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <h1 class="text-2xl font-bold text-gray-900">Excuse Letters</h1>
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
        <div class="<?= $contentCard ?> mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Filter Letters</h2>

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

            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="<?= $label ?>">Program</label>
                    <select name="course_name" id="course_name_filter" class="<?= $formInput ?>"
                        onchange="updateYearLevels()">
                        <option value="">All</option>
                        <?php foreach (array_unique(array_column($courses, 'course_name')) as $cn): ?>
                            <option value="<?= htmlspecialchars($cn) ?>" <?= $courseName === $cn ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cn) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="<?= $label ?>">Year Level</label>
                    <select name="year_level" id="year_level_filter" class="<?= $formInput ?>"
                        onchange="updateSections()">
                        <option value="">All</option>
                    </select>
                </div>
                <div>
                    <label class="<?= $label ?>">Section</label>
                    <select name="section" id="section_filter" class="<?= $formInput ?>">
                        <option value="">All</option>
                    </select>
                </div>
                <div>
                    <label class="<?= $label ?>">Status</label>
                    <select name="status" class="<?= $formInput ?>">
                        <option value="">All</option>
                        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="<?= $button ?> w-full">Apply Filters</button>
                </div>
            </form>
        </div>

        <div class="<?= $contentCard ?>">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Excuse Letters</h2>
            <?php if (empty($letterRows)): ?>
                <p class="text-gray-500">No excuse letters found.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($letterRows as $row): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= date('F j, Y g:i A', strtotime($row['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($row['last_name'] . ', ' . $row['first_name']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($row['course_name'] . ' ' . $row['year_level'] . ($row['section'] ? '-' . $row['section'] : '')) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= htmlspecialchars($row['subject'] ?? '(No subject)') ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    <?= $row['status'] === 'approved' ? 'bg-green-100 text-green-800' : ($row['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <form method="POST" class="flex items-center space-x-2">
                                            <input type="hidden" name="letter_id" value="<?= (int) $row['letter_id'] ?>">
                                            <input type="text" name="admin_comment" placeholder="Comment (optional)"
                                                class="<?= $formInput ?>">
                                            <button name="action" value="approve"
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Approve</button>
                                            <button name="action" value="reject"
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="px-6 pb-6 text-sm text-gray-700">
                                        <div class="bg-gray-50 rounded p-4">
                                            <span class="font-semibold">Letter:</span>
                                            <div class="mt-2 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($row['body'])) ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const allCourses = <?= json_encode($courses) ?>;
        const initialCourse = <?= json_encode($courseName) ?>;
        const initialYear = <?= json_encode($yearLevel) ?>;
        const initialSection = <?= json_encode($section) ?>;

        function updateYearLevels() {
            const courseSelect = document.getElementById('course_name_filter');
            const yearSelect = document.getElementById('year_level_filter');
            const sectionSelect = document.getElementById('section_filter');
            yearSelect.innerHTML = '<option value="">All</option>';
            sectionSelect.innerHTML = '<option value="">All</option>';
            const cn = courseSelect.value;
            if (!cn) return;
            const years = [...new Set(allCourses.filter(c => c.course_name === cn).map(c => c.year_level))];
            years.forEach(y => {
                const opt = document.createElement('option');
                opt.value = y;
                opt.textContent = y;
                yearSelect.appendChild(opt);
            });
        }

        function updateSections() {
            const courseSelect = document.getElementById('course_name_filter');
            const yearSelect = document.getElementById('year_level_filter');
            const sectionSelect = document.getElementById('section_filter');
            sectionSelect.innerHTML = '<option value="">All</option>';
            const cn = courseSelect.value;
            const yl = yearSelect.value;
            if (!cn || !yl) return;
            const sections = [...new Set(allCourses.filter(c => c.course_name === cn && c.year_level === yl).map(c => c
                .section).filter(Boolean))];
            sections.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s;
                opt.textContent = s;
                sectionSelect.appendChild(opt);
            });
        }
        // Initialize selects with current filters
        updateYearLevels();
        document.getElementById('course_name_filter').value = initialCourse || '';
        updateYearLevels();
        document.getElementById('year_level_filter').value = initialYear || '';
        updateSections();
        document.getElementById('section_filter').value = initialSection || '';
    </script>
</body>

</html>