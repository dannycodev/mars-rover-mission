<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RoverService;

class RoverController extends Controller
{
    protected $roverService;

    public function __construct(RoverService $roverService)
    {
        $this->roverService = $roverService;
    }

    public function move(Request $request)
    {
        // Validar la petición
        $request->validate([
            'commands' => 'required|string',
            'initialPosition' => 'required|array',
            'initialPosition.x' => 'required|integer',
            'initialPosition.y' => 'required|integer',
            'initialPosition.direction' => 'required|string|in:N,E,S,W',
            'obstacles' => 'array'
        ]);

        // Procesar comandos con el servicio
        $result = $this->roverService->processCommands(
            $request->input('commands'),
            $request->input('initialPosition'),
            $request->input('obstacles', [])
        );

        // Devolver resultado
        return response()->json($result);
    }
}