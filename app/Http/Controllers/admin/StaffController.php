<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function __construct()
    {
        // Proteksi Manual: Jika bukan owner, langsung tendang balik atau kasih error 403
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'owner') {
                abort(403, 'Unauthorized action. Only Daniel (Owner) can access this page.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        // Menampilkan daftar semua admin yang ada
        $staffs = User::where('role', 'admin')->get();
        return view('admin.staff.index', compact('staffs'));
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
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin', // Otomatis menjadi admin
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Admin baru berhasil ditambahkan!');
    }
}