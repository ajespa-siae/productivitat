<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAdmin(User $user): bool
    {
        return $user->is_admin;
    }
}
