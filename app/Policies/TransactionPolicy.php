<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Transaction;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return true; 
    }

    public function view(User $user, Transaction $tx): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array(optional($user->role)->role_name, ['Admin Gudang','Super Admin']);
    }

    public function update(User $user, Transaction $tx): bool
    {
        return in_array(optional($user->role)->role_name, ['Admin Gudang','Super Admin']);
    }

    public function delete(User $user, Transaction $tx): bool
    {
        return in_array(optional($user->role)->role_name, ['Admin Gudang','Super Admin']);
    }
}
