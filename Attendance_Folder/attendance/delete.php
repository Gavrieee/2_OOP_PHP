<?php
require_once __DIR__ . '../../Utilities/bootstrap.php';
require_once __DIR__ . '../../Utilities/tailwind_classes.php';

require_once __DIR__ . '../../Utilities/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance_id'])) {
    $attendance = new Attendance();
    $attendance->delete((int) $_POST['attendance_id']);
    // Redirect back to the list page after deletion
    header('Location: ' . ATTENDANCE_URL . 'read.php');
    exit;
} else {
    // Optionally handle invalid access
    header('Location: ' . ATTENDANCE_URL . 'read.php');
    exit;
}