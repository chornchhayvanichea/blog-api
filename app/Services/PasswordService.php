<?php

namespace App\Services;

use App\Models\User;
use App\Http\Requests\AuthRequests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordService
{
    private function validateCurrentPassword($current_password)
    {
        return Hash::check($current_password, auth()->user()->password);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        // Validate current password
        if (!$this->validateCurrentPassword($request->current_password)) {
            throw new \Exception('Current password is incorrect', 401);
        }

        // Update password
        auth()->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return [
            'status' => 'success',
            'message' => 'Password changed successfully'
        ];
    }

    public function sendResetLink($email)
    {
        $status = Password::sendResetLink(['email' => $email]);
        if ($status !== Password::RESET_LINK_SENT) {
            throw new \Exception('Failed to send reset link', 400);
        }

        return [
            'status' => 'success',
            'message' => 'Password reset link sent to your email'
        ];
    }

    public function resetPassword($data)
    {
        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->update(['password' => Hash::make($password)]);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new \Exception('Invalid or expired reset token', 400);
        }

        return [
            'status' => 'success',
            'message' => 'Password reset successfully'
        ];
    }
}
