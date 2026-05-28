<div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1a1a1a; max-width: 650px; margin: 0 auto; padding: 25px; background-color: #ffffff; border: 1px solid #e8e8e8; border-radius: 8px;">
    <h2 style="font-family: Georgia, serif; font-size: 20px; letter-spacing: 0.03em; border-bottom: 2px solid #121212; padding-bottom: 12px; text-transform: uppercase; margin-top: 0;">VESTA SALES REPORT SUMMARY</h2>
    
    <p style="font-size: 14px; line-height: 1.6; color: #333333;">Dear Management,</p>
    <p style="font-size: 14px; line-height: 1.6; color: #333333;">Berikut adalah laporan resume data transaksi penjualan VESTA yang berhasil ditarik secara dinamis dari database sistem oleh background worker.</p>

    @if ($format === 'xlsx')
        <div style="background-color: #f4f5f7; border-left: 4px solid #121212; padding: 15px; margin: 25px 0; border-radius: 4px;">
            <strong style="display: block; margin-bottom: 6px; font-size: 14px; color: #121212;">📌 Berkas Lampiran Tersedia</strong>
            <span style="font-size: 13px; color: #555555;">Laporan berformat <strong>Microsoft Excel (.xlsx)</strong> telah berhasil dirakit di background memori dan disematkan langsung sebagai attachment resmi pada email ini. Silakan cek bagian bawah lampiran surel Anda.</span>
        </div>
    @else
        <h3 style="font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 30px; margin-bottom: 10px; color: #555555;">📊 Ringkasan Tabel Penjualan Aktif</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left; margin-bottom: 25px; font-size: 13px;">
            <thead>
                <tr style="background-color: #121212; color: #ffffff;">
                    <th style="padding: 10px; border: 1px solid #121212; font-weight: bold;">INVOICE ID</th>
                    <th style="padding: 10px; border: 1px solid #121212; font-weight: bold;">CUSTOMER</th>
                    <th style="padding: 10px; border: 1px solid #121212; font-weight: bold;">QTY</th>
                    <th style="padding: 10px; border: 1px solid #121212; font-weight: bold;">REVENUE</th>
                </tr>
            </thead>
            <tbody>
                @if($transactions && $transactions->count() > 0)
                    @foreach ($transactions as $t)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 10px; border: 1px solid #e5e7eb; font-weight: 500;">#{{ $t->id }}</td>
                        <td style="padding: 10px; border: 1px solid #e5e7eb; color: #4b5563;">{{ $t->user->name ?? 'GUEST' }}</td>
                        <td style="padding: 10px; border: 1px solid #e5e7eb; color: #4b5563;">{{ $t->quantity }} PCS</td>
                        <td style="padding: 10px; border: 1px solid #e5e7eb; text-align: right; font-weight: 500; color: #121212;">IDR {{ number_format($t->total_price, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" style="padding: 15px; border: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-style: italic;">Tidak ada data transaksi pada periode ini.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    <div style="border-top: 1px dashed #cbd5e1; margin-top: 35px; padding-top: 15px; font-size: 12px; color: #64748b; line-height: 1.7;">
        <strong style="color: #475569; display: block; margin-bottom: 4px;">Executive Metadata Summary:</strong>
        • Total Transaksi Sukses: {{ $transactions ? $transactions->count() : 0 }} Record(s)<br>
        • Total Akumulasi Omset: <strong>IDR {{ number_format($transactions ? $transactions->sum('total_price') : 0, 0, ',', '.') }}</strong><br>
        • Environment Server Host: {{ config('app.url') }}
    </div>

    <p style="font-size: 12px; color: #94a3b8; margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 10px; font-style: italic;">
        Regards,<br>
        <strong>VESTA Automated System Engine</strong>
    </p>
</div>