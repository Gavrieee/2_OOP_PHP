<?php
require_once __DIR__ . '/../Utilities/tailwind_classes.php';
require_once __DIR__ . '/../Utilities/bootstrap.php';
require_once __DIR__ . '/../Utilities/config.php';
function attendance_read()
{
    include __DIR__ . '/../attendance/read.php';
}

function attendance_create()
{
    include __DIR__ . '/../attendance/create.php';
}

function attendance_update()
{
    include __DIR__ . '/../attendance/update.php';
}

function attendance_delete()
{
    include __DIR__ . '/../attendance/delete.php';
}

// New function that shows both create and read forms
function attendance_dashboard()
{
    include __DIR__ . '/../attendance/create.php';
    include __DIR__ . '/../attendance/read.php';
}