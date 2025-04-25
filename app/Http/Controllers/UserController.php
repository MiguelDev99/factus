<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->get('auth_user');
        return response()->json($user);
    }

    public function update(Request $request)
    {
        $user = $request->get('auth_user');

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'identification' => 'nullable|string',
            'dv' => 'nullable|string',
            'company' => 'nullable|string',
            'trade_name' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'legal_organization_id' => 'nullable|string',
            'tribute_id' => 'nullable|string',
            'identification_document_id' => 'nullable|string',
            'municipality_id' => 'nullable|string',
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user' => $user,
        ]);
    }
}
