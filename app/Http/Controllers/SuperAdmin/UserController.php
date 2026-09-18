<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\SuperAdmin\UserRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna beserta perannya.
     */
    public function index(): View
    {
        $users = User::with('roles')->latest()->paginate(10);
        return view('super-admin.users.index', compact('users'));
    }

    /**
     * Menampilkan formulir tambah pengguna baru.
     */
    public function create(): View
    {
        $roles = Role::orderBy('name')->get();
        return view('super-admin.users.create', compact('roles'));
    }

    /**
     * Menyimpan pengguna baru ke basis data.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $roleName = $payload['role'];
        unset($payload['role']);

        $payload['password'] = Hash::make($payload['password']);

        $user = User::create($payload);
        $user->assignRole($roleName);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'Akun staf baru berhasil didaftarkan.');
    }

    /**
     * Menampilkan formulir edit pengguna dan perannya.
     */
    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();
        return view('super-admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Memperbarui informasi pengguna dan perannya.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $payload = $request->validated();
        $roleName = $payload['role'];
        unset($payload['role']);

        if (!empty($payload['password'])) {
            $payload['password'] = Hash::make($payload['password']);
        } else {
            unset($payload['password']);
        }

        $user->update($payload);
        $user->syncRoles([$roleName]);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'Data akun staf berhasil diperbarui.');
    }

    /**
     * Menghapus akun pengguna.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('super-admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->shifts()->whereNull('closed_at')->exists()) {
            return redirect()->route('super-admin.users.index')
                ->with('error', 'Staf masih memiliki shift kasir yang aktif. Harap tutup shift terlebih dahulu.');
        }

        $user->delete();

        return redirect()->route('super-admin.users.index')
            ->with('success', 'Akun staf berhasil dihapus dari sistem.');
    }
}