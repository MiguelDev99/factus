<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\FacturacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FactusTestController extends Controller
{
    public function probarToken(FacturacionService $factus)
    {
        try {
            $accessToken = $factus->getAccessToken();
            
            return response()->json([
                'message' => 'Token generado correctamente',
                'access_token' => $accessToken
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener token: ' . $e->getMessage());

            return response()->json([
                'message' => 'Fallo al obtener token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
