<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ResetCodePassword;
use Illuminate\Http\Request;
use App\Mail\SendResetPassword;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users',
            'notification' => 'required|boolean'
        ]);

        // Delete all old code that the user sent before.
        ResetCodePassword::where('email', $request->email)->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::create($data);

        // Send email to user
        // IMPORTANT => Configure your email credentials in .env
        if (isset($data['notification']) && $data['notification']) {
            Mail::to($request->email)->send(new SendResetPassword($codeData->code));
            return response([
                'status'  => 'ok',
                'message' => 'Message to resent password is send.'
            ]);
        } else {
            return response([
                'status'  => 'ok',
                'message' => 'Notification not send. Just show the validation code here.',
                'body'    => [
                    'code'    => $codeData->code
                ]
            ]);
        }
    }

    public function passwordCodeCheck(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'code' => 'required|string|exists:reset_code_passwords',
        ]);

        // find the code
        $passwordReset = ResetCodePassword::where('code', $request->code)
            ->where('email', $request->email)
            ->first();

        //Check if it has not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            return response([
                'status'  => 'error',
                'message' => 'Code is expired.'
            ], 422);
        }
        
        return response([
            'status'  => 'ok',
            'message' => 'Code is valid.'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'code' => 'required|string|exists:reset_code_passwords',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // find the code
        $passwordReset = ResetCodePassword::where('code', $request->code)
            ->where('email', $request->email)
            ->first();

        //Check if it has not expired: the time is one hour
        if ($passwordReset->created_at > now()->addHour()) {
            $passwordReset->delete();
            return response([
                'status'  => 'error',
                'message' => 'Code is expired.'
            ], 422);
        }

        // find user's email 
        $user = User::firstWhere('email', $passwordReset->email);

        // update user password
        $user->update($request->only('password'));

        // delete current code 
        $passwordReset->delete();

        return response([
            'status'  => 'ok',
            'message' => 'Password has been successfully reset.'
        ]);
    }
}