<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        //search and pagination
        $keyword = $request->query('search');

        $users = User::with(['roles', 'permissions'])
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function ($q2) use ($keyword) {
                    $q2->where('name',  'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
                });
            })
            ->latest()
            ->paginate(5)
            ->appends(['search' => $keyword]);
            
        return view('admin.users.index', compact('users', 'keyword'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email|max:255',
            'role'      => 'required|exists:roles,name',
            'password'  => 'required|min:8|confirmed',
        ], [
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus.',
            'role.exists' => 'Role yang dipilih tidak valid.',
        ]);

        // Hapus role dari validated data karena akan diassign terpisah
        $role = $validated['role'];
        unset($validated['role']);
        
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->assignRole($role);

        return back()->with('success', 'User berhasil ditambahkan!');
    }

    public function show(User $user)
    {
        // Jika diperlukan untuk menampilkan detail user dalam halaman terpisah
        return view('admin.users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email,' . $user->id . '|max:255',
            'role'      => 'required|exists:roles,name',
        ];
        
        // Validasi password hanya jika diisi
        if ($request->filled('password')) {
            $rules['password'] = 'required|min:8';
        }

        $validated = $request->validate($rules, [
            'name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus.',
            'role.exists' => 'Role yang dipilih tidak valid.',
        ]);

        // Handle role assignment
        $role = $validated['role'];
        unset($validated['role']);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        
        // Sync role (remove all roles and assign new one)
        $user->syncRoles([$role]);

        return back()->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        // Opsional: cegah admin menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun Anda sendiri!');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus!');
    }
}
