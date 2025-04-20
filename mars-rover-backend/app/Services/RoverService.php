<?php

namespace App\Services;

use App\Models\Rover;
use App\Models\Grid;

class RoverService
{
    public function processCommands($commands, $initialPosition, $obstacles)
    {
        // Crear instancias de Grid y Rover
        $grid = new Grid(20, 20, $obstacles);
        $rover = new Rover(
            $initialPosition['x'],
            $initialPosition['y'],
            $initialPosition['direction']
        );

        // Procesar cada comando
        $obstacleFound = false;
        $obstaclePosition = null;

        foreach (str_split(strtoupper($commands)) as $command) {
            // Determinar la próxima posición
            $nextPosition = $this->getNextPosition($rover, $command);
            
            // Si es un movimiento hacia adelante, verificar obstáculos
            if ($command === 'F' && $nextPosition) {
                if ($grid->hasObstacle($nextPosition['x'], $nextPosition['y'])) {
                    $obstacleFound = true;
                    $obstaclePosition = [
                        'x' => $nextPosition['x'],
                        'y' => $nextPosition['y']
                    ];
                    break;
                }
                
                // Actualizar posición
                $rover->setPosition($nextPosition['x'], $nextPosition['y']);
            } elseif ($command === 'L' || $command === 'R') {
                // Actualizar dirección
                $rover->rotate($command);
            }
        }

        // Preparar respuesta
        $response = [
            'success' => !$obstacleFound,
            'position' => [
                'x' => $rover->getX(),
                'y' => $rover->getY(),
                'direction' => $rover->getDirection()
            ]
        ];

        if ($obstacleFound) {
            $response['obstaclePosition'] = $obstaclePosition;
        }

        return $response;
    }

    private function getNextPosition($rover, $command)
    {
        if ($command !== 'F') {
            return null;
        }

        $x = $rover->getX();
        $y = $rover->getY();
        $direction = $rover->getDirection();

        switch ($direction) {
            case 'N':
                $y--;
                break;
            case 'E':
                $x++;
                break;
            case 'S':
                $y++;
                break;
            case 'W':
                $x--;
                break;
        }

        return [
            'x' => $x,
            'y' => $y
        ];
    }
}