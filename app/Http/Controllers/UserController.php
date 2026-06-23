<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class UserController extends Controller
{
    public function index()
    {
        // Menampilkan seluruh daftar user aktif agar Direktur bisa memantau semuanya
        // Diurutkan berdasarkan hierarki: direktur, keuangan, warehouse, sales
        $users = User::orderByRaw("
            CASE 
                WHEN role = 'direktur' THEN 1 
                WHEN role = 'manager' THEN 2
                WHEN role = 'admin_keuangan' OR role = 'keuangan' THEN 3 
                WHEN role = 'admin_warehouse' OR role = 'warehouse' THEN 4 
                WHEN role = 'sales' THEN 5 
                ELSE 6 
            END
        ")->orderBy('name', 'asc')->get();
                     
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,direktur,sales,admin_warehouse,admin_keuangan,manager',
            'hak_akses' => 'nullable|array', // Tambahkan validasi array untuk hak akses
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            // Jika ada centangan, simpan. Jika kosong/tidak dicentang, simpan array kosong []
            'hak_akses' => $request->hak_akses ?? [], 
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'TAMBAH AKUN STAF',
            'description' => Auth::user()->name . ' membuat akun staf baru: ' . $request->email,
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'User baru berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,direktur,sales,admin_warehouse,admin_keuangan,manager',
            'hak_akses' => 'nullable|array', // Tambahkan validasi array untuk hak akses
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            // Jika ada centangan, simpan. Jika kosong/tidak dicentang, simpan array kosong []
            'hak_akses' => $request->hak_akses ?? [], 
        ];

        // Jika password diisi, maka update passwordnya
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'EDIT AKUN STAF',
            'description' => Auth::user()->name . ' memperbarui informasi akun/hak akses untuk: ' . $user->email,
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'User ' . $user->name . ' berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Mencegah menghapus diri sendiri
        if (Auth::id() == $user->id) {
            return back()->withErrors(['error' => 'Anda tidak bisa menghapus akun Anda sendiri!']);
        }

        $emailUser = $user->email;
        $user->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'HAPUS AKUN STAF',
            'description' => Auth::user()->name . ' menghapus akun staf: ' . $emailUser,
            'ip_address' => request()->ip(),
        ]);

        return back()->with('success', 'User berhasil dihapus.');
    }
}