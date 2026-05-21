<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function __construct()
    {
        // REVISI: Izinkan Owner DAN Manager untuk masuk ke controller ini
        $this->middleware(function ($request, $next) {
            $authorizedRoles = ['owner', 'manager'];
            
            if (!in_array(Auth::user()->role, $authorizedRoles)) {
                abort(403, 'Unauthorized action. Only Owner and Manager can access this page.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $admins = User::whereIn('role', ['owner', 'manager', 'staff'])
                      ->orderByRaw("FIELD(role, 'owner', 'manager', 'staff')")
                      ->get();

        return view('admin.staff.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:manager,staff',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active',
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff baru berhasil direkrut!');
    }

    public function edit($id) 
    {
        $staff = User::findOrFail($id);
        $currentUser = Auth::user();

        // REVISI PROTEKSI: Manager tidak boleh edit Owner atau sesama Manager
        if ($currentUser->role === 'manager' && ($staff->role === 'owner' || $staff->role === 'manager')) {
            if ($staff->id !== $currentUser->id) { // Kecuali edit profil sendiri jika diizinkan
                abort(403, 'Managers can only edit Staff accounts.');
            }
        }

        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $staff = User::findOrFail($id);
        $currentUser = Auth::user();

        // Proteksi yang sama untuk proses update
        if ($currentUser->role === 'manager' && ($staff->role === 'owner' || $staff->role === 'manager')) {
            if ($staff->id !== $currentUser->id) {
                abort(403, 'Unauthorized update.');
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:owner,manager,staff',
        ]);

        $staff->name = $request->name;
        $staff->email = $request->email;
        
        // Hanya Owner yang boleh mengubah Role orang lain
        if ($currentUser->role === 'owner') {
            $staff->role = $request->role;
        }

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        $staff->save();
        return redirect()->route('admin.staff.index')->with('success', 'Data personil berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $staff = User::findOrFail($id);
        $currentUser = Auth::user();

        // REVISI PROTEKSI: Manager hanya bisa hapus role 'staff'
        if ($currentUser->role === 'manager' && $staff->role !== 'staff') {
            return redirect()->back()->with('error', 'Manager hanya boleh mencabut akses level Staff.');
        }

        if ($staff->role === 'owner') {
            return redirect()->back()->with('error', 'Akun Owner utama tidak dapat dihapus.');
        }

        $staff->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Akses personil berhasil dicabut.');
    }
}