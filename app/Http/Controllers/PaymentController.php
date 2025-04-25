<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        // Supongamos que ya tienes estos datos del frontend o API externa
        $data = $request->validate([
            'payment_method' => 'required|string',
            'payment_status' => 'required|string|max:1',
            'bill_number' => 'nullable|string',
            'total' => 'nullable|numeric',
            'billed_at' => 'nullable|date',
        ]);

        $payment = Payment::create($data);

        return response()->json([
            'message' => 'Pago registrado correctamente',
            'payment' => $payment,
        ]);
    }
}
