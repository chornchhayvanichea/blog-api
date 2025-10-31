<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequests\ChangePasswordRequest;
use App\Http\Requests\AuthRequests\ForgotPasswordRequest;
use App\Http\Requests\AuthRequests\LoginRequest;
use App\Http\Requests\AuthRequests\ResetPasswordRequest;
use App\Http\Requests\AuthRequests\SignupRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\PasswordService;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
        $this->middleware('auth:api', ['except' => [
             'login',
             'signup',
             'changePassword',
             'forgotPassword',
             'resetPassword'
         ]
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $token = auth('api')->attempt($credentials);

        if (! $token) {
            return $this->sendError('Unauthorized', [], 401);
        }

        return $this->sendResponse([
            'user' => new UserResource(auth()->user()),
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful');
    }

    public function signup(SignupRequest $request)
    {
        $validated = $request->validated();
        $user = User::create([
            'name' => $validated['name'],
            'password' => Hash::make($validated['password']),
            'email' => $validated['email'],
        ]);
        $profileData = [
            'bio' => $validated['bio'] ?? null,
        ];
        if ($request->hasFile('avatar')) {
            $profileData['avatar'] = $request->file('avatar')->store('profiles', 'public');
        }
        $user->profile()->create($profileData);
        return $this->sendResponse([
            'user' => $user,
        ], 'User signed up Successfully');
    }

    public function logout()
    {
        auth('api')->logout();
        return $this->sendResponse([], 'User has logged out Successfully');
    }

    public function refreshToken()
    {
        $token = auth('api')->refresh();

        return $this->sendResponse([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ], 'Token refreshed successfully');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $result = $this->passwordService->changePassword($request);

            return $this->sendResponse($result, 'Password changed successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], $e->getCode() ?: 400);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $result = $this->passwordService->sendResetLink($request->email);

            return $this->sendResponse($result, 'Password reset link sent successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], $e->getCode() ?: 400);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $result = $this->passwordService->resetPassword($request->validated());

            return $this->sendResponse($result, 'Password has been reset successfully');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], $e->getCode() ?: 400);
        }
    }
}
