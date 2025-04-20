<?php

namespace App\Models;

class Grid
{
    private $width;
    private $height;
    private $obstacles;

    public function __construct($width, $height, $obstacles)
    {
        $this->width = $width;
        $this->height = $height;
        $this->obstacles = $obstacles;
    }

    public function hasObstacle($x, $y)
    {
        // Verificar si la posición está fuera de los límites
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            return true;
        }

        // Verificar si hay un obstáculo en esta posición
        foreach ($this->obstacles as $obstacle) {
            if ($obstacle['x'] === $x && $obstacle['y'] === $y) {
                return true;
            }
        }

        return false;
    }
}