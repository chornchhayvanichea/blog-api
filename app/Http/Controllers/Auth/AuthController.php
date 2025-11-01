<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequests\ChangePasswordRequest;
use App\Http\Requests\AuthRequests\ForgotPasswordRequest;
use App\Http\Requests\AuthRequests\LoginRequest;
use App\Http\Requests\AuthRequests\ResetPasswordRequest;
use App\Http\Requests\AuthRequests\SignupRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\PasswordService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    private $passwordService;

    public function __construct(PasswordService $passwordService)
    {
        $this->passwordService = $passwordService;
        $this->middleware('auth:api', ['except' => [
            'login',
            'signup',
            'forgotPassword',
            'resetPassword',
            'index'
        ]]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $token = auth('api')->attempt($credentials);

        if (!$token) {
            return $this->sendError('Unauthorized', [], 401);
        }
        $user = auth()->user()->load('profile');
        return $this->sendResponse([
            'user' => new UserResource($user),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
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

        // Generate token for automatic login after signup
        $token = auth('api')->login($user);

        return $this->sendResponse([
            'user' => new UserResource($user->load('profile')),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ], 'User signed up successfully');
    }


    public function show()
    {
        $user = User::with(['profile'])
            ->withCount(['posts', 'receivedLikes', 'comments']) // adds posts_count, likes_count, comments_count
            ->find(auth()->id());

        return $this->sendResponse([
            'user' => new UserResource($user),
        ], 'User retrieved successfully');
    }


    public function logout()
    {
        auth('api')->logout();
        return $this->sendResponse([], 'User has logged out successfully');
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
    public function index()
    {
        $user = User::with(['profile'])->withCount(['posts','comments','receivedLikes'])->get();
        return $this->sendResponse([
            'users' => UserResource::collection($user),
        ], 'User retrieved successfully.');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($user->profile && $user->profile->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }
            $avatarPath = $request->file('avatar')->store('profiles', 'public');
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                ['avatar' => $avatarPath]
            );
            unset($validated['avatar']);
        }

        if (isset($validated['bio'])) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                ['bio' => $validated['bio']]
            );
            unset($validated['bio']);
        }

        $user->update($validated);
        $user->load('profile');

        return $this->sendResponse([
            'user' => new UserResource($user)
        ], 'User has been updated successfully');
    }
}
