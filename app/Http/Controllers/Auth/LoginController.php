<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        //busca el usuario en la base de datos.
        //Busca el primer usuario que tenga el mismo correo que se recibió en el request.
        $user = User::where('email', $request->email)->first();

        //Verifica si el usuario existe y si la contraseña coincide
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        //Crea el payload del JWT
        $payload = [
            'iss' => "your-app-name", // Emisor del token
            'sub' => $user->id,       // ID del usuario
            'iat' => Carbon::now()->timestamp,            // Momento en que se generó
            'exp' => Carbon::now()->addHours(3)->timestamp, // Expira en 3 horas
        ];        

        //Crea el token JWT
        $jwt = JWT::encode($payload, env('JWT_SECRET'), 'HS256');

        return response()->json([
            'token' => $jwt,
            'user'  => $user
        ]);
    }
}
