<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher; // <--- Pastikan kamu sudah membuat model Voucher ini
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
    /**
     * Menampilkan daftar semua voucher promo VESTA.
     */
    public function index(): View
    {
        // Eager load jika ada relasi, urutkan dari yang terbaru, dan batasi halaman demi performa
        $vouchers = Voucher::latest()->paginate(10);

        return view('admin.vouchers.index', compact('vouchers'));
    }

    /**
     * Menampilkan form untuk membuat voucher baru.
     */
    public function create(): View
    {
        return view('admin.vouchers.create');
    }

    /**
     * Menyimpan voucher baru yang dibuat ke dalam database.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input Data yang Ketat
        $validated = $request->validate([
            'code'         => 'required|string|unique:vouchers,code|max:50|alpha_num', // Kode harus unik, kombinasi huruf & angka
            'type'         => 'required|in:fixed,percentage',
            'reward_value' => 'required|numeric|min:1',
            'total_quota'  => 'required|integer|min:1',
            'expired_at'   => 'required|date|after:today', // Batas waktu wajib di masa depan
        ], [
            'code.unique' => 'The voucher code has already been taken. Please create a unique one.',
            'code.alpha_num' => 'The voucher code may only contain letters and numbers without spaces.',
            'expired_at.after' => 'The expiry date must be a date after today.',
        ]);

        // Proteksi Tambahan untuk tipe Persentase (Diskon tidak boleh > 100%)
        if ($validated['type'] === 'percentage' && $validated['reward_value'] > 100) {
            return back()->withErrors(['reward_value' => 'Percentage discount cannot exceed 100%.'])->withInput();
        }

        // 2. Gunakan DB Transaction untuk menjamin keamanan penyimpanan data kaku
        DB::beginTransaction();
        try {
            // Ubah kode voucher otomatis menjadi huruf kapital semua (Uppercase Standard)
            $validated['code'] = strtoupper($validated['code']);
            $validated['used_quota'] = 0; // Set awal pemakaian ke angka 0

            Voucher::create($validated);

            DB::commit();
            return redirect()->route('admin.vouchers.index')->with('success', 'Luxury promotion voucher created successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Create Voucher Error: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong while creating the voucher. Please try again.')->withInput();
        }
    }

    /**
     * Menampilkan form edit untuk voucher tertentu.
     */
    public function edit($id): View
    {
        $voucher = Voucher::findOrFail($id);
        return view('admin.vouchers.edit', compact('voucher'));
    }

    /**
     * Memperbarui data voucher di database.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $voucher = Voucher::findOrFail($id);

        // Validasi, pastikan kode unik mengabaikan ID voucher itu sendiri saat di-update
        $validated = $request->validate([
            'code'         => 'required|string|max:50|alpha_num|unique:vouchers,code,' . $voucher->id,
            'type'         => 'required|in:fixed,percentage',
            'reward_value' => 'required|numeric|min:1',
            'total_quota'  => 'required|integer|min: ' . $voucher->used_quota, // Kuota baru tidak boleh lebih kecil dari yang sudah terpakai
            'expired_at'   => 'required|date',
        ], [
            'total_quota.min' => 'The total quota cannot be lower than the already used quota (' . $voucher->used_quota . ' used).',
        ]);

        if ($validated['type'] === 'percentage' && $validated['reward_value'] > 100) {
            return back()->withErrors(['reward_value' => 'Percentage discount cannot exceed 100%.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $validated['code'] = strtoupper($validated['code']);
            $voucher->update($validated);

            DB::commit();
            return redirect()->route('admin.vouchers.index')->with('success', 'Promotion voucher updated successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Voucher Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update voucher. Details logged.')->withInput();
        }
    }

    /**
     * Menghapus voucher secara permanen dari database.
     */
    public function destroy($id): RedirectResponse
    {
        $voucher = Voucher::findOrFail($id);

        DB::beginTransaction();
        try {
            $voucher->delete();

            DB::commit();
            return redirect()->route('admin.vouchers.index')->with('success', 'Voucher deleted permanently.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Voucher Error: ' . $e->getMessage());
            return redirect()->route('admin.vouchers.index')->with('error', 'Failed to delete the voucher.');
        }
    }
}