<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    public function index(Request $request)
    {
        $search  = $request->get('q');
        $roleId  = $request->get('role_id');
        $unitId  = $request->get('unit_id');

        $q = User::with(['role','unit']);

        if ($search) {
            $q->where(function($qq) use ($search){
                $qq->where('name','like',"%{$search}%")
                   ->orWhere('email','like',"%{$search}%");
            });
        }

        if ($roleId) {
            $q->where('role_id', $roleId);
        }

        if ($unitId) {
            $q->where('unit_id', $unitId);
        }

        $users = $q->orderBy('name')
                   ->paginate(20)
                   ->withQueryString();
        $roles = Role::orderBy('role_name')->pluck('role_name','id');
        $units = Unit::orderBy('unit_name')->pluck('unit_name','id');

        if ($request->wantsJson()) {
            return response()->json([
                'data'   => $users->items(),
                'meta'   => [
                    'total'        => $users->total(),
                    'per_page'     => $users->perPage(),
                    'current_page' => $users->currentPage(),
                ],
            ]);
        }

        return view('users.index', [
            'users'   => $users,
            'roles'   => $roles,
            'units'   => $units,
            'search'  => $search,
            'roleId'  => $roleId,
            'unitId'  => $unitId,
        ]);
    }

    public function create()
    {
        $roles = Role::orderBy('role_name')->pluck('role_name','id');
        $units = Unit::orderBy('unit_name')->pluck('unit_name','id');

        return view('users.create', compact('roles','units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id'  => 'required|exists:roles,id',
            'unit_id'  => 'required|exists:units,id',
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data)->load(['role','unit']);

        if ($request->wantsJson()) {
            return response()->json($user, 201);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'User baru berhasil dibuat.');
    }

    public function show(User $user, Request $request)
    {
        $user->load(['role','unit']);

        if ($request->wantsJson()) {
            return response()->json($user);
        }

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load(['role','unit']);
        $roles = Role::orderBy('role_name')->pluck('role_name','id');
        $units = Unit::orderBy('unit_name')->pluck('unit_name','id');

        return view('users.edit', compact('user','roles','units'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => [
                'required','email','max:255',
                Rule::unique('users','email')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:6',
            'role_id'  => 'required|exists:roles,id',
            'unit_id'  => 'required|exists:units,id',
        ]);

        $originalIsSuperAdmin = $user->isSuperAdmin();
        if ($originalIsSuperAdmin && (int) $data['role_id'] !== (int) $user->role_id) {
            $otherSuperAdminCount = User::where('id','!=',$user->id)
                ->whereHas('role', function($q){
                    $q->whereRaw('LOWER(role_name) = ?', ['super admin']);
                })
                ->count();

            if ($otherSuperAdminCount === 0) {
                return back()
                    ->withErrors(['role_id' => 'Tidak boleh mengubah role Super Admin terakhir.'])
                    ->withInput();
            }
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        $user->load(['role','unit']);

        if ($request->wantsJson()) {
            return response()->json($user);
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            $msg = 'Anda tidak boleh menghapus akun Anda sendiri.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 422);
            }
            return back()->withErrors(['delete' => $msg]);
        }
        
        if ($user->isSuperAdmin()) {
            $otherSuperAdmins = User::where('id','!=',$user->id)
                ->whereHas('role', function($q){
                    $q->whereRaw('LOWER(role_name) = ?', ['super admin']);
                })
                ->count();

            if ($otherSuperAdmins === 0) {
                $msg = 'Tidak boleh menghapus Super Admin terakhir.';
                if ($request->wantsJson()) {
                    return response()->json(['message' => $msg], 422);
                }
                return back()->withErrors(['delete' => $msg]);
            }
        }

        $user->delete();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()
            ->route('users.index')
            ->with('success','User berhasil dihapus.');
    }
}
