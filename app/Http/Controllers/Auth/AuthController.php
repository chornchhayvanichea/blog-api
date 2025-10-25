<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequests\ForgotPasswordRequest;
use App\Http\Requests\AuthRequests\SignupRequest;
use App\Http\Requests\AuthRequests\LoginRequest;
use App\Http\Requests\AuthRequests\ChangePasswordRequest;
use App\Http\Requests\AuthRequests\ResetPasswordRequest;
use App\Services\PasswordService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $passwordService;
    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
        $this->middleware('auth:api', ['except' => ['login','signup','changePassword','forgotPassword','resetPassword']]);
    }

    public function login(LoginRequest $request)
    {
        $token = auth('api')->attempt($request->validated());

        if (!$token) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid credentials provided'
            ], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'status' => 'success',
        ], 200);
    }

    public function signup(SignupRequest $request)
    {
        $validated = $request->validated();
        $user = User::create([
            'name' => $validated['name'],
            'password' => Hash::make($validated['password']),
            'email' => $validated['email']
        ]);
        $profileData = [
            'bio' => $validated['bio'] ?? null
        ];
        if ($request->hasFile('avatar')) {
            $profileData['avatar'] = $request->file('avatar')->store('profiles', 'public');
        }
        $user->profile()->create($profileData);
        return response()->json([
            'status' => 'success',
            'message' => 'User has been created successfully',
            'user' => $user->load('profile')
        ], 201);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out'
        ]);
    }

    public function refreshToken()
    {
        return response()->json([
            'access_token' => auth('api')->refresh(),
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $result = $this->passwordService->changePassword($request);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $result = $this->passwordService->sendResetLink($request->email);
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $result = $this->passwordService->resetPassword($request->validated());
            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], $e->getCode() ?: 400);
        }
    }


}
