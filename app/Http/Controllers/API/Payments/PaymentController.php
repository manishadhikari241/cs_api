<?php

namespace App\Http\Controllers\API\Payments;

use App\Http\Controllers\Controller;
use App\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller {

    public function show(Request $request, $userId) {
        return Payment::where('user_id', $userId)->orderBy('id', 'desc')->get();
    }

}
