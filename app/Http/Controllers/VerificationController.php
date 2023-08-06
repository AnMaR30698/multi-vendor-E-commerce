<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\VerificationHelper;
use Illuminate\Http\Response;

class VerificationController extends Controller
{
    public function sendVerificationCode(Request $request, VerificationHelper $verificationHelper)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $phoneNumber = $request->phone;

        if ($verificationHelper->sendVerificationCode($phoneNumber)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Verification code sent successfully.'
            ],Response::HTTP_OK);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send verification code.'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
