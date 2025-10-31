<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function index(User $user, Report $report)
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Report $report)
    {
        return $user->role === 'admin';
    }
}
