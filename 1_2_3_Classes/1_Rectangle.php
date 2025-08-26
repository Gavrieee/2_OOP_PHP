<?php

// 1. Design a class named Rectangle to represent a rectangle. The class contains: 

// Two double data fields named width and height that specify the width and height of the rectangle. The default values are 1 for both width and height. 

// A constructor that creates a rectangle with the specified width and height. 

// Write a method named getArea() that returns the area of this rectangle and a method named getPerimeter() that returns the perimeter.

class Rectangle
{
    private $width;
    private $height;

    // Constructor with default values of 1 for both width and height
    public function __construct($width = 1, $height = 1)
    {
        $this->width = $width;
        $this->height = $height;
    }

    // Getter methods for Width and Height for flexibility
    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    // Getter methods for Area and Perimeter
    public function getArea()
    {
        // Area = width * height
        return $this->getWidth() * $this->getHeight();
    }

    public function getPerimeter()
    {
        // Perimeter = 2 * (width + height)
        return 2 * ($this->getWidth() + $this->getHeight());
    }
}

// Example usage with set values
$rectangle = new Rectangle(5, 10);
echo "Area: " . $rectangle->getArea() . "<br>"; // Outputs: Area: 50
echo "Perimeter: " . $rectangle->getPerimeter() . "<br>"; // Outputs: Perimeter: 30

// Example usage with default values of 1:1
$defaultRectangle = new Rectangle();
echo "Area: " . $defaultRectangle->getArea() . "<br>"; // Outputs: Area: 1
echo "Perimeter: " . $defaultRectangle->getPerimeter() . "<br>"; // Outputs: Perimeter: 4