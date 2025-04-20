<?php

namespace App\Models;

class Rover
{
    private $x;
    private $y;
    private $direction;
    private $directionMap = ['N', 'E', 'S', 'W'];

    public function __construct($x, $y, $direction)
    {
        $this->x = $x;
        $this->y = $y;
        $this->direction = $direction;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function setPosition($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function rotate($command)
    {
        // Encontrar índice actual de la dirección
        $currentIndex = array_search($this->direction, $this->directionMap);
        
        // Calcular nueva dirección
        if ($command === 'R') {
            $newIndex = ($currentIndex + 1) % 4;
        } else { // L
            $newIndex = ($currentIndex - 1 + 4) % 4;
        }
        
        $this->direction = $this->directionMap[$newIndex];
    }
}