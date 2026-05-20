<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Console Routes & Task Scheduling (Laravel 11 Style)
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * CRON JOB MEWAH: Evaluasi Tier Loyalty Pelanggan VESTA
 * Berjalan otomatis di latar belakang setiap tengah malam (00:00).
 * Menurunkan atau menaikkan tier secara adil berdasarkan pengeluaran 365 hari terakhir.
 */
Schedule::call(function () {
    User::where('role', 'customer')->chunk(100, function ($users) {
        foreach ($users as $user) {
            $user->updateMembershipTier();
        }
    });
})
->daily()
->name('vesta:evaluate-loyalty-tiers'); // <--- Tambahkan nama identifier di sini