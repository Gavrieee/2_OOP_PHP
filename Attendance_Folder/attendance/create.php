<?php
if (!defined('APP_RUNNING')) {
    header("Location: ../../index.php");
    exit;
}
?>

<div class="max-w p-6 bg-white rounded-lg shadow-md">
    <form action="Attendance_Folder/Controllers/attendance_form.php" method="post">
        <h2 class="text-2xl font-bold mb-6 text-center">Attendance Form</h2>

        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-semibold mb-2">Name</label>
            <input type="text" id="name" name="name" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4">
            <label for="date" class="block text-gray-700 font-semibold mb-2">Date</label>
            <input type="date" id="date" name="date" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="mb-4">
            <label for="status" class="block text-gray-700 font-semibold mb-2">Status</label>
            <select id="status" name="status" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" disabled selected>Select status</option>
                <option value="1">Present</option>
                <option value="2">Absent</option>
                <option value="3">Late</option>
            </select>
        </div>

        <input type="hidden" name="action" value="create">

        <button type="submit"
            class="w-full bg-blue-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition duration-300">
            Submit
        </button>
    </form>
</div>