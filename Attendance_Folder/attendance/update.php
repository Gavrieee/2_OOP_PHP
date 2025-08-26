<?php
// if (!defined('APP_RUNNING')) {
//     header("Location: ../../index.php");
// }

require_once __DIR__ . '../../Utilities/bootstrap.php';
require_once __DIR__ . '../../Utilities/tailwind_classes.php';

require_once __DIR__ . '../../Utilities/config.php';

// Initialize model
$attendance = new Attendance();

$id = $_GET['attendance_id'] ?? null;
if ($id) {
    $record = $attendance->findById('attendance', $id, 'attendance_id');
}

?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Form</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="<?= $body2 ?>">
    <div class="<?= $mainCard2 ?>">

        <h2 class="<?= $h2 ?>">Edit Mode</h2>

        <?php if ($record): ?>
            <form action="../Controllers/attendance_form.php" method="POST">

                <div class="mb-4">
                    <label for="name" class="<?= $label ?>">Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($record['name']) ?>"
                        class="<?= $formInput ?>">
                </div>
                <div class="mb-4">
                    <label for="date" class="<?= $label ?>">Date</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($record['date']) ?>"
                        class="<?= $formInput ?>">
                </div>
                <div class="mb-4">
                    <label for="status" class="<?= $label ?>">Status</label>
                    <select name="status" class="<?= $formInput ?>">
                        <option value="1" <?= $record['status'] == 1 ? 'selected' : '' ?>>Present</option>
                        <option value="2" <?= $record['status'] == 2 ? 'selected' : '' ?>>Absent</option>
                        <option value="3" <?= $record['status'] == 3 ? 'selected' : '' ?>>Late</option>
                    </select>
                </div>

                <input type="hidden" name="action" value="update">
                <input type="hidden" name="attendance_id" value="<?= $record['attendance_id'] ?>">
                <button type="submit" class="<?= $button ?>">Update</button>
            </form>
        <?php endif; ?>

    </div>
</body>

</html>