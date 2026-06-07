<?php

namespace App\Mail;

use App\Exports\SalesReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class FinancialReportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $format;
    public $period;
    
    /**
     * Properti publik untuk menampung data transaksi hasil query background worker
     */
    public $transactions;

    /**
     * Constructor Baru: Hanya membawa tipe data primitif yang 100% aman masuk antrean database
     */
    public function __construct($format, $period)
    {
        $this->format = $format;
        $this->period = (int) $period;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📊 VESTA Dashboard Internal Sales Report - Executive Summary',
        );
    }

    /**
     * KUNCI FIX HTML: Menarik data transaksi secara real-time di memori RAM server 
     * tepat sebelum background worker merakit komponen Blade template.
     */
    public function prepareMailable()
    {
        $exportInstance = new SalesReportExport($this->period);
        $this->transactions = $exportInstance->query()->get();

        return $this;
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.report',
        );
    }

    public function attachments(): array
    {
        if ($this->format === 'xlsx') {
            return [
                Attachment::fromData(
                    fn () => Excel::raw(new SalesReportExport($this->period), \Maatwebsite\Excel\Excel::XLSX),
                    'VESTA_Sales_Report_' . date('Ymd_His') . '.xlsx'
                )->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ];
        }

        return [];
    }
}
