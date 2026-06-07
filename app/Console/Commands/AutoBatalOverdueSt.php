<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoBatalOverdueSt extends Command
{
    protected $signature = 'st:auto-batal';
    protected $description = 'Ubah status ST menjadi Batal jika sudah 30 hari tidak ada aktivitas sejak end_date terlewat';

    public function handle(): void
    {
        $batasInaktif = Carbon::now()->subDays(30);
        $statusAktif  = ['Final', 'Tidak Aktif', 'Batal'];

        $jumlah = DB::table('d_st')
            ->where('end_date', '<', Carbon::today()->toDateString())
            ->whereNotIn('status_st', $statusAktif)
            ->where('updated_at', '<', $batasInaktif->toDateTimeString())
            ->update([
                'status_st'  => 'Batal',
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);

        $this->info("Auto-batal: {$jumlah} ST diubah menjadi Batal.");
    }
}
