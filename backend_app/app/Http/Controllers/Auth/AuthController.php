<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class AuthController extends Controller
{
    /** Create a new account*/
    public function register(Request $request)
    {
        // Laravel provides us an easy way to validate such passwords with the Password Rule.
        $fields = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
        ]);
        
        $user = User::create($fields);

        $token = $user->createToken('new_account_token');

        return response([
            'status'  => 'ok',
            'message' => 'Correct registration.',
            'body'    => [
                'user'    => $user,
                'token'   => $token->plainTextToken
            ]
        ]);
    }

    
    /** Log in*/
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'status'  => 'error',
                'message' => 'The provided credentials are incorrect.'
            ], 400);
        }

        $token = $user->createToken('login_access_token');

        return response([
            'status'  => 'ok',
            'message' => 'Correct login.',
            'body'    => [
                'user'    => $user,
                'token'   => $token->plainTextToken
            ]
        ]);
    }

    
    /** Refresh token*/
    public function refreshToken(Request $request)
    {
        // create new token
        $accessToken = $request->user()->createToken('refresh_access_token');
        // delete current token
        $oldToken = $request->user()->currentAccessToken();
        $request->user()->tokens()->where('id', $oldToken->id)->delete();
        
        return response([
            'status'  => 'ok',
            'message' => 'Correct token refresh.',
            'body'    => [
                'token' => $accessToken->plainTextToken
            ]
        ]);
    }

    
    /** Log out from all sessions*/
    public function logoutAll(Request $request)
    {
        // Revoke all tokens...
        $request->user()->tokens()->delete();

        return response([
            'status'  => 'ok',
            'message' => 'Correct logout (all tokens).'
        ]);
    }

    
    /** Log out from current session*/
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request...
        $request->user()->currentAccessToken()->delete();

        return response([
            'status'  => 'ok',
            'message' => 'Correct logout (current token).'
        ]);
    }
}