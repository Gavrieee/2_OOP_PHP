<?php
if (!defined('APP_RUNNING')) {
    header("Location: ../../index.php");
}

require_once __DIR__ . '../../Utilities/bootstrap.php';
require_once __DIR__ . '../../Utilities/tailwind_classes.php';

require_once __DIR__ . '../../Utilities/config.php';

// Initialize model
$attendance = new Attendance();

// Fetch all records
$records = $attendance->read();

$statusMap = [
    1 => ["label" => "Present", "style" => "text-green-600 font-bold"],
    2 => ["label" => "Absent", "style" => "text-red-600 font-bold"],
    3 => ["label" => "Late", "style" => "text-yellow-600 font-bold"],
];

?>

<div class="<?= $mainCard ?> ">

    <h2 class="<?= $h2 ?>">Attendances</h2>

    <div class="overflow-auto ">
        <table
            class="table-auto min-w-full text-center border border-separate border-spacing-4 border-gray-300 rounded-lg whitespace-nowrap">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Student's Name</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $row): ?>
                    <tr>
                        <!-- $row['attendance_id'] -->
                        <td class="<?= $statusMap[$row['status']]['style'] ?? "" ?>">
                            <?= htmlspecialchars($statusMap[$row['status']]['label'] ?? "Unknown") ?>
                        </td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td class="flex justify-center gap-3">
                            <a href="<?= ATTENDANCE_URL ?>update.php?action=edit&attendance_id=<?= $row['attendance_id'] ?>"
                                class="cursor-pointer" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>

                            <form action="<?= ATTENDANCE_URL ?>delete.php" method="POST" style="display:inline;">
                                <input type="hidden" name="attendance_id" value="<?= $row['attendance_id'] ?>">
                                <button type="submit" onclick="return confirm('Delete this record?')" class="cursor-pointer"
                                    title="Delete"><i class="fa-solid fa-delete-left"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>