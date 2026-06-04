<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminManagementController extends Controller
{
    public function index()
    {
        // Hanya Owner yang bisa melihat daftar semua staff admin
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Otoritas tidak cukup.');
        }

        $admins = User::where('role', 'admin')->get();
        return view('admin.staff.index', compact('admins'));
    }

    public function create()
    {
        // Hanya Owner yang bisa membuka form tambah admin
        if (auth()->user()->role !== 'owner') {
            abort(403);
        }
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        // Proteksi ganda di level sistem
        if (auth()->user()->role !== 'owner') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin', // Dikunci otomatis sebagai admin
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Admin baru berhasil didaftarkan.');
    }
}