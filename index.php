<?php
define('APP_RUNNING', true);

require_once __DIR__ . '/Attendance_Folder/Utilities/tailwind_classes.php';


?>


<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Form</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<!-- 4.	Use a PDO object and then create three classes: Database, Attendance and Student class. Write an Object Oriented code in PHP that enables Create, Read, Update and Delete (CRUD) operations. Keep in mind here that the Database class is our superclass and should store the methods for CRUD. The attendance and student class will just inherit methods from the class. Include the necessary HTML templates for the CRUD functionality.  -->

<body class="<?= $body ?>">

    <?php include 'Attendance_Folder/attendance/create.php' ?>

    <?php include 'Attendance_Folder/attendance/read.php' ?>

</body>

</html>