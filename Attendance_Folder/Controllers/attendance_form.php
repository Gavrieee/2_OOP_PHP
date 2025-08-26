<?php

require_once __DIR__ . '/../Classes/Attendance.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? $_GET['action'] ?? 'update'; // default to create
    $errors = [];
    $attendance = new Attendance();

    switch ($action) {
        case 'create':
            $name = trim($_POST['name'] ?? '');
            $date = trim($_POST['date'] ?? '');
            $status = $_POST['status'] ?? '';

            // Basic validation
            if ($name === '') {
                $errors[] = "Name is required.";
            }
            if ($date === '') {
                $errors[] = "Date is required.";
            }
            if (!in_array($status, ['1', '2', '3'])) {
                $errors[] = "Invalid status selected.";
            }

            if (empty($errors)) {
                try {
                    $attendance->create([
                        'name' => $name,
                        'date' => $date,
                        'status' => $status
                    ]);
                    header("Location: ../../index.php");
                    exit;
                } catch (Exception $e) {
                    $errors[] = "Error saving attendance: " . $e->getMessage();
                }
            }
            break;

        case 'update':
            $id = $_POST['attendance_id'] ?? null;
            $name = trim($_POST['name'] ?? '');
            $date = trim($_POST['date'] ?? '');
            $status = $_POST['status'] ?? '';

            if (!$id)
                $errors[] = "Invalid ID.";

            if (empty($errors)) {
                try {
                    $attendance->update($id, [
                        'name' => $name,
                        'date' => $date,
                        'status' => $status
                    ]);
                    header("Location: ../../index.php?updated=1");
                    exit;
                } catch (Exception $e) {
                    $errors[] = "Error updating attendance: " . $e->getMessage();
                }
            }
            break;


        default:
            $errors[] = "Unknown action: {$action}";

            break;
    }

    header("Location: ../../index.php");
    exit;




}