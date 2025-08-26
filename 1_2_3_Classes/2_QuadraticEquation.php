<?php

// Design a class named QuadraticEquation for a quadratic equation ax^2 + bx + x = 0. The class contains:

//1 Private data fields a, b, and c that represent three coefficients.

//2 A constructor for the arguments for a, b, and c.

//3 Three getter methods for a, b, and c.

//4 A method named getDiscriminant() that returns the discriminant, which is bp — 4ac.

//5 The methods named getRoot1() and getRoot2 () for returning two roots of the equation

class QuadraticEquation
{

    private $a;
    private $b;
    private $c;

    public function __construct($a, $b, $c)
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }

    // Getter methods for a, b, and c
    public function getA()
    {
        return $this->a;
    }

    public function getB()
    {
        return $this->b;
    }

    public function getC()
    {
        return $this->c;
    }

    // Method to calculate the discriminant

    public function getDiscriminant()
    {
        return (pow($this->getB(), 2)) - 4 * $this->getA() * $this->getC();
    }

    public function getRoot1()
    {
        $getDiscriminant = $this->getDiscriminant();

        if ($getDiscriminant < 0) {
            return "No real roots";
        }

        return ($this->getB() + sqrt($getDiscriminant)) / 2 * $this->getA();
    }

    public function getRoot2()
    {
        $getDiscriminant = $this->getDiscriminant();

        if ($getDiscriminant < 0) {
            return "No real roots";
        }

        return ($this->getB() - sqrt($getDiscriminant)) / (2 * $this->getA());
    }
}

$equation = new QuadraticEquation(1, -2, -5);
echo "Root 1: " . $equation->getRoot1() . "<br>";
echo "Root 2: " . $equation->getRoot2() . "<br>";

echo "Discriminant: " . $equation->getDiscriminant() . "<br>";