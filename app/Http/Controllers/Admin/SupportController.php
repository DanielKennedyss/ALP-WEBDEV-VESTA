<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupportReplyMail;

class SupportController extends Controller
{
    /**
     * Menampilkan daftar semua tiket support masuk
     */
    public function index()
    {
        $inquiries = ContactInquiry::orderBy('created_at', 'desc')->get();
        return view('admin.support.index', compact('inquiries'));
    }

    /**
     * Menampilkan detail isi satu pesan support khusus
     */
    public function show($id)
    {
        $inquiry = ContactInquiry::findOrFail($id);
        return view('admin.support.show', compact('inquiry'));
    }

    /**
     * Memproses pengiriman email balasan ke customer dan memperbarui status di database
     */
    public function reply(Request $request, $id)
    {
        $inquiry = ContactInquiry::findOrFail($id);

        $request->validate([
            'reply_message' => 'required|string|max:10000',
        ]);

        $customerName = $inquiry->first_name . ' ' . $inquiry->last_name;

        // 1. Kirim Email Balasan Nyata ke Alamat Email Pengirim Support
        Mail::to($inquiry->email)->send(new SupportReplyMail(
            $customerName,
            $inquiry->subject,
            $request->reply_message
        ));

        // 2. Perbarui Data di Database agar Status Berubah dari PENDING menjadi REPLIED
        $inquiry->update([
            'status' => 'REPLIED',
            'reply_message' => $request->reply_message
        ]);

        return redirect()->route('admin.support.index')->with('success', 'Reply has been sent to customer successfully.');
    }
}