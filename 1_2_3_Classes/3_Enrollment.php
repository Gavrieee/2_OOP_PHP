<?php

// Write an Object Oriented code in PHP that simulates a student object being enrolled and having courses/subjects added under his name. 

// A method that enables deleting of courses should be included in the code. 

// One course should cost 1450 PHP. 

// Upon execution of the code, we should be able to see the total enrollment fee. 

class Student
{
    private $name;
    private $courses = [
        'Mathematics',
        'Science',
        'History',
        'Art',
        'Physical Education',
        'CLE',
        'Literature'
    ];

    private $COURSE_FEE = 1450;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getStudentName()
    {
        return $this->name;
    }

    public function getCourses()
    {
        return $this->courses;
    }

    public function deleteCourse(...$courses)
    {
        foreach ($courses as $course_name) {
            $index = array_search($course_name, $this->courses);

            if ($index !== false) {
                unset($this->courses[$index]);
            }
        }
        $this->courses = array_values($this->courses);
    }

    public function getTotalFee()
    {
        return count($this->getCourses()) * $this->COURSE_FEE;
    }
}

// Example usage
$student = new Student("Gavrie Talaboc");
echo $student->getStudentName() . "<br>";
$student->deleteCourse("Art", "Science", "CLE");
echo "Courses Enrolled: " . implode(", ", $student->getCourses()) . "<br>";
echo "Total Enrollment Fee: PHP " . $student->getTotalFee() . "<br>";