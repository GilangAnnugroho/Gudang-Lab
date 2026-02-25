<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Request as RequestModel;

class RequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, RequestModel $request): bool
    {
        $role = optional($user->role)->role_name;

        if (in_array($role, ['Super Admin', 'Admin Gudang', 'Kepala Lab'])) {
            return true;
        }

        return $request->unit_id === $user->unit_id;
    }
    
    public function create(User $user): bool
    {
        return in_array(optional($user->role)->role_name, [
            'Petugas Unit',
            'Super Admin',
            'Admin Gudang',
        ]);
    }

    public function update(User $user, RequestModel $request): bool
    {
        $isOwner = $request->request_user_id === $user->id;

        return $isOwner && $request->status === RequestModel::STATUS_PENDING;
    }

    public function delete(User $user, RequestModel $request): bool
    {
        $isOwner = $request->request_user_id === $user->id;

        return $isOwner && $request->status === RequestModel::STATUS_PENDING;
    }

    public function approve(User $user, RequestModel $request): bool
    {
        return in_array(optional($user->role)->role_name, [
                'Super Admin',
                'Kepala Lab',
            ]) && $request->status === RequestModel::STATUS_PENDING;
    }

    public function reject(User $user, RequestModel $request): bool
    {
        return in_array(optional($user->role)->role_name, [
                'Super Admin',
                'Kepala Lab',
            ]) && $request->status === RequestModel::STATUS_PENDING;
    }

    public function distribute(User $user, RequestModel $request): bool
    {
        return in_array(optional($user->role)->role_name, [
                'Admin Gudang',
                'Super Admin',
            ]) && $request->status === RequestModel::STATUS_APPROVED;
    }
}
