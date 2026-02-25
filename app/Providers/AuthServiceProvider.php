<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Models\Request as RequestModel;
use App\Models\Transaction;
use App\Models\ItemMaster;
use App\Policies\RequestPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\ItemMasterPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        RequestModel::class   => RequestPolicy::class,
        Transaction::class    => TransactionPolicy::class,
        ItemMaster::class     => ItemMasterPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('is-super', fn(User $u) => optional($u->role)->role_name === 'Super Admin');
        Gate::define('is-admin-gudang', fn(User $u) => optional($u->role)->role_name === 'Admin Gudang');
        Gate::define('is-kepala', fn(User $u) => optional($u->role)->role_name === 'Kepala Lab');
        Gate::define('is-petugas', fn(User $u) => optional($u->role)->role_name === 'Petugas Unit');
        Gate::define('manage-master', fn(User $u) =>
            in_array(optional($u->role)->role_name, ['Super Admin', 'Admin Gudang'])
        );

        Gate::define('manage-users', fn(User $u) =>
            optional($u->role)->role_name === 'Super Admin'
        );

        Gate::define('make-transaction', fn(User $u) =>
            in_array(optional($u->role)->role_name, ['Super Admin', 'Admin Gudang'])
        );

        Gate::define('request-approve', fn(User $u) =>
            in_array(optional($u->role)->role_name, ['Super Admin', 'Admin Gudang', 'Kepala Lab'])
        );

        Gate::define('request-distribute', fn(User $u) =>
            in_array(optional($u->role)->role_name, ['Super Admin', 'Admin Gudang'])
        );
    }
}
