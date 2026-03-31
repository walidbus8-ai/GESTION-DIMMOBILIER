<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // دالة التسجيل (التي كانت ناقصة)
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'motDePasse' => 'required|string|min:8',
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'motDePasse' => Hash::make($request->motDePasse),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'motDePasse' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->motDePasse, $user->motDePasse)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function profile(Request $request)
    {
        return $request->user();
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'nom' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'motDePasse' => 'sometimes|min:6',
            'preferences' => 'sometimes|array',
        ]);

        if (isset($data['motDePasse'])) {
            $data['motDePasse'] = Hash::make($data['motDePasse']);
        }

        $user->update($data);
        return response()->json($user);
    }

    public function recommendations(Request $request)
    {
        // تأكدي أن دالة obtenirRecommandations() معرفة داخل موديل User
        return $request->user()->obtenirRecommandations();
    }
}