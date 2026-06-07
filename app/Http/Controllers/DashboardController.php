<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\StExport;
use App\Exports\LhpExport;
use App\Exports\BidwasExport;
use App\Exports\DailyExport;
use App\Exports\MonthlyExport;

class DashboardController extends Controller
{
    /** Status yang dianggap ST sudah tuntas/selesai. */
    private const STATUS_SELESAI = ['Final', 'Tidak Aktif'];

    /** Status yang TIDAK dihitung terlambat (sudah selesai atau dibatalkan). */
    private const STATUS_TAK_OVERDUE = ['Final', 'Tidak Aktif', 'Batal'];

    /** Maksimum ST aktif yang boleh dipegang satu pegawai secara bersamaan. */
    private const MAX_ST_AKTIF = 3;

    /** Role yang dianggap level pimpinan dan mendapat tampilan overview leadership. */
    private const ROLE_PIMPINAN = ['pimpinan', 'kabid'];

    public function index()
    {
        $hariIni = Carbon::today()->toDateString();
        $tahunIni = Carbon::now()->year;
        $penggunaAktif = auth()->user();
        $bidangPengguna = $this->bidangIdPengguna($penggunaAktif);
        $isKabidDivisi = $penggunaAktif->role === 'kabid' && $bidangPengguna;

        $roleEfektif = ($penggunaAktif->role === 'kabid' && !$bidangPengguna)
            ? 'pegawai'
            : $penggunaAktif->role;

        $querySuratTugasAktif = DB::table('d_st')
            ->where('start_date', '<=', $hariIni)
            ->where('end_date', '>=', $hariIni)
            ->whereIn('status_st', ['Realisasi', 'Perpanjangan ST']);

        if ($roleEfektif === 'pegawai') {
            $querySuratTugasAktif->whereExists(function ($permintaan) use ($penggunaAktif) {
                $permintaan->select(DB::raw(1))
                    ->from('d_st_tim')
                    ->whereColumn('d_st_tim.id_st', 'd_st.id_st')
                    ->where('d_st_tim.nip', $penggunaAktif->nip);
            });
        } elseif ($isKabidDivisi) {
            $querySuratTugasAktif->where('id_bidwas', $bidangPengguna);
        }
        $totalSuratTugasAktif = $querySuratTugasAktif->count();

        $pegawaiPalingSibuk           = collect();
        $pegawaiTersedia              = collect();
        $daftarSuratTugasBelumSelesai = collect();
        $totalBelumSelesai            = 0;
        $totalPegawaiTersedia         = 0;
        $isPimpinanLevel              = in_array($roleEfektif, self::ROLE_PIMPINAN);

        if ($isPimpinanLevel) {
            $bebanKerjaPegawaiQuery = DB::table('d_st_tim')
                ->join('d_st', 'd_st_tim.id_st', '=', 'd_st.id_st')
                ->join('r_pegawai', 'd_st_tim.nip', '=', 'r_pegawai.nip')
                ->whereIn('d_st.status_st', ['Realisasi', 'Perpanjangan ST']);

            if ($isKabidDivisi) {
                $bebanKerjaPegawaiQuery->where('r_pegawai.id_bidwas', $bidangPengguna);
            }

            $pegawaiPalingSibuk = (clone $bebanKerjaPegawaiQuery)
                ->select('r_pegawai.nama', 'r_pegawai.nip', DB::raw('count(d_st_tim.id_st) as active_tasks'))
                ->groupBy('r_pegawai.nama', 'r_pegawai.nip')
                ->having('active_tasks', '>=', 2)
                ->orderByDesc('active_tasks')
                ->limit(5)
                ->get();

            $nipPegawaiSibukHariIni = DB::table('d_st_tim')
                ->join('d_st', 'd_st_tim.id_st', '=', 'd_st.id_st')
                ->where('d_st.start_date', '<=', $hariIni)
                ->where('d_st.end_date', '>=', $hariIni)
                ->whereIn('d_st.status_st', ['Realisasi', 'Perpanjangan ST'])
                ->pluck('d_st_tim.nip');
            $nipPegawaiSibuk = $nipPegawaiSibukHariIni;

            $queryPegawaiTersedia = DB::table('r_pegawai')->whereNotIn('nip', $nipPegawaiSibuk);
            if ($isKabidDivisi) {
                $queryPegawaiTersedia->where('id_bidwas', $bidangPengguna);
            }
            $totalPegawaiTersedia = (clone $queryPegawaiTersedia)->count();
            $pegawaiTersedia = $queryPegawaiTersedia->select('nama', 'nip')->take(10)->get();

            $querySuratTugasBelumSelesai = DB::table('d_st')
                ->where('end_date', '<', $hariIni)
                ->whereNotIn('status_st', self::STATUS_TAK_OVERDUE);
            if ($isKabidDivisi) {
                $querySuratTugasBelumSelesai->where('d_st.id_bidwas', $bidangPengguna);
            }
            $daftarSuratTugasBelumSelesai = (clone $querySuratTugasBelumSelesai)
                ->orderBy('end_date', 'asc')->take(5)->get();
            $totalBelumSelesai = (clone $querySuratTugasBelumSelesai)->count();
        }

        $statistikBidwas = DB::table('r_bidwas')
            ->leftJoin('d_st', 'r_bidwas.id_bidwas', '=', 'd_st.id_bidwas')
            ->select('r_bidwas.kd_bidwas as kode', 'r_bidwas.nm_bidwas as nama', DB::raw('count(d_st.id_st) as total'))
            ->groupBy('r_bidwas.id_bidwas', 'r_bidwas.kd_bidwas', 'r_bidwas.nm_bidwas')
            ->get();

        $queryTrendBulanan = DB::table('d_st')
            ->select(DB::raw('MONTH(start_date) as bulan'), DB::raw('count(*) as total'))
            ->whereYear('start_date', $tahunIni);

        if ($roleEfektif === 'pegawai') {
            $queryTrendBulanan->whereExists(function ($permintaan) use ($penggunaAktif) {
                $permintaan->select(DB::raw(1))
                    ->from('d_st_tim')
                    ->whereColumn('d_st_tim.id_st', 'd_st.id_st')
                    ->where('d_st_tim.nip', $penggunaAktif->nip);
            });
        }
        $trendBulanan = $queryTrendBulanan->groupBy('bulan')->orderBy('bulan')->get();

        $queryDistribusiStatus = DB::table('d_st')
            ->select('status_st', DB::raw('count(*) as total'));

        if ($roleEfektif === 'pegawai') {
            $queryDistribusiStatus->whereExists(function ($query) use ($penggunaAktif) {
                $query->select(DB::raw(1))
                    ->from('d_st_tim')
                    ->whereColumn('d_st_tim.id_st', 'd_st.id_st')
                    ->where('d_st_tim.nip', $penggunaAktif->nip);
            });
        }
        $distribusiStatus = $queryDistribusiStatus->groupBy('status_st')->get();

        if ($isKabidDivisi) {
            $totalPegawai = DB::table('r_pegawai')->where('id_bidwas', $bidangPengguna)->count();
            $totalLHP = DB::table('d_lhp')
                ->join('d_st', 'd_lhp.id_st', '=', 'd_st.id_st')
                ->where('d_st.id_bidwas', $bidangPengguna)
                ->count();
        } else {
            $totalPegawai = DB::table('r_pegawai')->count();
            $totalLHP = DB::table('d_lhp')->count();
        }
        $targetPKPT = DB::table('r_pkpt')->count();

        $labelBidang = null;
        if ($isKabidDivisi) {
            $nm = DB::table('r_bidwas')->where('id_bidwas', $bidangPengguna)->value('nm_bidwas');
            $labelBidang = $this->namaBidangPendek($nm);
        }

        $statStaf = null;
        $daftarStSaya = collect();
        if ($roleEfektif === 'pegawai') {
            $daftarStSaya = DB::table('d_st')
                ->join('d_st_tim', 'd_st.id_st', '=', 'd_st_tim.id_st')
                ->where('d_st_tim.nip', $penggunaAktif->nip)
                ->select('d_st.id_st', 'd_st.nama_penugasan', 'd_st.no_surat_tugas',
                         'd_st.start_date', 'd_st.end_date', 'd_st.status_st', 'd_st_tim.peran')
                ->orderBy('d_st.start_date', 'desc')
                ->limit(8)
                ->get();

            $idStSaya = DB::table('d_st_tim')->where('nip', $penggunaAktif->nip)->pluck('id_st')->unique();
            $statStaf = [
                'total_st'  => $idStSaya->count(),
                'st_aktif'  => $idStSaya->isEmpty() ? 0 : DB::table('d_st')
                    ->whereIn('id_st', $idStSaya)
                    ->whereIn('status_st', ['Realisasi', 'Perpanjangan ST'])
                    ->count(),
                'selesai'   => $idStSaya->isEmpty() ? 0 : DB::table('d_st')
                    ->whereIn('id_st', $idStSaya)
                    ->whereIn('status_st', self::STATUS_SELESAI)
                    ->count(),
            ];
        }

        $role = $roleEfektif;
        $scopeSt = function ($q) use ($role, $isKabidDivisi, $bidangPengguna, $penggunaAktif) {
            if ($role === 'pegawai') {
                $q->whereExists(function ($e) use ($penggunaAktif) {
                    $e->select(DB::raw(1))->from('d_st_tim')
                        ->whereColumn('d_st_tim.id_st', 'd_st.id_st')
                        ->where('d_st_tim.nip', $penggunaAktif->nip);
                });
            } elseif ($isKabidDivisi) {
                $q->where('d_st.id_bidwas', $bidangPengguna);
            }
            return $q;
        };

        $stTotalScope = $scopeSt(DB::table('d_st'))->count();
        $stSelesai    = $scopeSt(DB::table('d_st'))->whereIn('status_st', self::STATUS_SELESAI)->count();
        $stOverdue    = $scopeSt(DB::table('d_st'))->where('end_date', '<', $hariIni)
                            ->whereNotIn('status_st', self::STATUS_TAK_OVERDUE)->count();
        $stDenganLhp  = $scopeSt(DB::table('d_st'))->whereExists(function ($e) {
                            $e->select(DB::raw(1))->from('d_lhp')->whereColumn('d_lhp.id_st', 'd_st.id_st');
                        })->count();

        $pctPenyelesaian = $stTotalScope ? (int) round($stSelesai / $stTotalScope * 100) : 0;
        $pctKetepatan    = $stTotalScope ? (int) round(($stTotalScope - $stOverdue) / $stTotalScope * 100) : 0;
        $pctRasioLhp     = $stTotalScope ? (int) round($stDenganLhp / $stTotalScope * 100) : 0;

        if ($role === 'pegawai') {
            $metrik4 = ['label' => 'ST Aktif', 'nilai' => $totalSuratTugasAktif, 'bar' => null,
                        'sub' => 'Penugasan saya hari ini', 'icon' => 'clipboard-list', 'warna' => 'purple'];
        } else {
            $busyQ = DB::table('d_st_tim')->join('d_st', 'd_st_tim.id_st', '=', 'd_st.id_st')
                ->where('d_st.start_date', '<=', $hariIni)->where('d_st.end_date', '>=', $hariIni)
                ->whereIn('d_st.status_st', ['Realisasi', 'Perpanjangan ST']);
            if ($isKabidDivisi) {
                $busyQ->join('r_pegawai', 'd_st_tim.nip', '=', 'r_pegawai.nip')
                      ->where('r_pegawai.id_bidwas', $bidangPengguna);
            }
            $busyScope = $busyQ->distinct()->count('d_st_tim.nip');
            $pctUtil = $totalPegawai ? (int) round($busyScope / $totalPegawai * 100) : 0;
            $metrik4 = ['label' => 'Utilisasi Pegawai', 'nilai' => $pctUtil . '%', 'bar' => $pctUtil,
                        'sub' => $busyScope . ' dari ' . $totalPegawai . ' pegawai bertugas', 'icon' => 'users', 'warna' => 'purple'];
        }

        $metrik3 = $roleEfektif === 'pegawai'
            ? ['label' => 'ST Sedang Berjalan', 'nilai' => $statStaf['st_aktif'] ?? 0, 'bar' => null,
               'sub' => 'Penugasan aktif saat ini', 'icon' => 'activity', 'warna' => 'amber']
            : ['label' => 'Rasio Output LHP', 'nilai' => $pctRasioLhp . '%', 'bar' => $pctRasioLhp,
               'sub' => $stDenganLhp . ' dari ' . $stTotalScope . ' ST menghasilkan LHP', 'icon' => 'file-check', 'warna' => 'amber'];

        $metrikKinerja = [
            ['label' => 'Penyelesaian ST', 'nilai' => $pctPenyelesaian . '%', 'bar' => $pctPenyelesaian,
             'sub' => $stSelesai . ' dari ' . $stTotalScope . ' ST selesai', 'icon' => 'check-circle', 'warna' => 'blue'],
            ['label' => 'Ketepatan Jadwal', 'nilai' => $pctKetepatan . '%', 'bar' => $pctKetepatan,
             'sub' => $stOverdue . ' ST melewati tenggat', 'icon' => 'clock', 'warna' => 'emerald'],
            $metrik3,
            $metrik4,
        ];

        $ovBase = fn () => $scopeSt(DB::table('d_st'))
            ->where('end_date', '<', $hariIni)
            ->whereNotIn('status_st', self::STATUS_TAK_OVERDUE);
        $agingData = [
            $ovBase()->whereRaw('DATEDIFF(?, end_date) BETWEEN 1 AND 6', [$hariIni])->count(),
            $ovBase()->whereRaw('DATEDIFF(?, end_date) BETWEEN 7 AND 30', [$hariIni])->count(),
            $ovBase()->whereRaw('DATEDIFF(?, end_date) > 30', [$hariIni])->count(),
        ];

        $qTrendLhp = DB::table('d_lhp')->join('d_st', 'd_lhp.id_st', '=', 'd_st.id_st')
            ->select(DB::raw('MONTH(d_lhp.tanggal_lhp) as bulan'), DB::raw('count(*) as total'))
            ->whereYear('d_lhp.tanggal_lhp', $tahunIni);
        if ($role === 'pegawai') {
            $qTrendLhp->whereExists(function ($e) use ($penggunaAktif) {
                $e->select(DB::raw(1))->from('d_st_tim')
                    ->whereColumn('d_st_tim.id_st', 'd_st.id_st')
                    ->where('d_st_tim.nip', $penggunaAktif->nip);
            });
        } elseif ($isKabidDivisi) {
            $qTrendLhp->where('d_st.id_bidwas', $bidangPengguna);
        }
        $trendLhpBulanan = $qTrendLhp->groupBy('bulan')->orderBy('bulan')->get();

        $bebanPegawaiDivisi = collect();
        if ($isKabidDivisi) {
            $bebanPegawaiDivisi = DB::table('r_pegawai')
                ->leftJoin('d_st_tim', 'r_pegawai.nip', '=', 'd_st_tim.nip')
                ->leftJoin('d_st', function ($j) {
                    $j->on('d_st_tim.id_st', '=', 'd_st.id_st')
                      ->whereIn('d_st.status_st', ['Realisasi', 'Perpanjangan ST']);
                })
                ->where('r_pegawai.id_bidwas', $bidangPengguna)
                ->select('r_pegawai.nama', DB::raw('count(d_st.id_st) as total'))
                ->groupBy('r_pegawai.id', 'r_pegawai.nama')
                ->orderByDesc('total')
                ->limit(10)
                ->get();
        }

        $leaderboardBidang = collect();
        if ($role === 'pimpinan') {
            $leaderboardBidang = $this->getBidwasData()
                ->filter(fn ($b) => $b->total_st > 0)
                ->map(function ($b) {
                    $b->rasio = $b->total_st > 0 ? (int) round($b->total_lhp / $b->total_st * 100) : 0;
                    $b->nama_pendek = $this->namaBidangPendek($b->nama_bidwas);
                    return $b;
                })
                ->sortByDesc('rasio')
                ->values();
        }

        return view('dashboard.index', compact(
            'totalSuratTugasAktif',
            'totalPegawai',
            'totalLHP',
            'targetPKPT',
            'pegawaiPalingSibuk',
            'pegawaiTersedia',
            'totalPegawaiTersedia',
            'daftarSuratTugasBelumSelesai',
            'totalBelumSelesai',
            'statistikBidwas',
            'trendBulanan',
            'distribusiStatus',
            'labelBidang',
            'statStaf',
            'metrikKinerja',
            'trendLhpBulanan',
            'leaderboardBidang',
            'agingData',
            'bebanPegawaiDivisi',
            'daftarStSaya',
            'roleEfektif',
            'isPimpinanLevel'
        ));
    }

    public function apiTim($id)
    {
        $tim = DB::table('d_st_tim')->where('id_st', $id)->get(['nip', 'peran']);
        return response()->json($tim);
    }

    /**
     * Ubah nama bidang panjang jadi label pendek (konsisten dgn navbar).
     */
    private function namaBidangPendek(?string $nm): ?string
    {
        if (!$nm) return null;
        if (str_contains($nm, 'Instansi Pemerintah Pusat')) return 'IPP';
        if (str_contains($nm, 'Pemerintah Daerah'))         return 'APD';
        if (str_contains($nm, 'Akuntan Negara'))            return 'Akuntan Negara';
        if (str_contains($nm, 'Program dan Pelaporan'))     return 'P3A';
        if (str_contains($nm, 'Investigasi'))               return 'Investigasi';
        return $nm;
    }

    /**
     * Resolusi bidang_id pengguna: pakai users.bidang_id, fallback ke r_pegawai.id_bidwas via NIP.
     */
    private function bidangIdPengguna($user)
    {
        return $user->bidang_id
            ?: optional(DB::table('r_pegawai')->where('nip', $user->nip)->first('id_bidwas'))->id_bidwas;
    }

    public function ajaxAvailableEmployees(Request $request)
    {
        $hariIni = Carbon::today()->toDateString();
        $penggunaAktif = auth()->user();

        $nipPegawaiSibuk = DB::table('d_st_tim')
            ->join('d_st', 'd_st_tim.id_st', '=', 'd_st.id_st')
            ->where('d_st.start_date', '<=', $hariIni)
            ->where('d_st.end_date', '>=', $hariIni)
            ->whereIn('d_st.status_st', ['Realisasi', 'Perpanjangan ST'])
            ->pluck('d_st_tim.nip');

        $query = DB::table('r_pegawai')->whereNotIn('nip', $nipPegawaiSibuk);

        $bidangPengguna = $this->bidangIdPengguna($penggunaAktif);
        if ($penggunaAktif->role === 'pegawai') {
            $query->where('nip', $penggunaAktif->nip);
        } elseif ($penggunaAktif->role === 'kabid' && $bidangPengguna) {
            $query->where('id_bidwas', $bidangPengguna);
        }

        $pegawaiTersedia = $query->select('nama', 'nip')->paginate(50, ['*'], 'available_page');

        return view('dashboard.partials.available_employees', compact('pegawaiTersedia'))->render();
    }

    public function ajaxOverdueTasks(Request $request)
    {
        $hariIni = Carbon::today()->toDateString();
        $penggunaAktif = auth()->user();

        $query = DB::table('d_st')
            ->where('end_date', '<', $hariIni)
            ->whereNotIn('status_st', self::STATUS_TAK_OVERDUE);

        $bidangPengguna = $this->bidangIdPengguna($penggunaAktif);
        if ($penggunaAktif->role === 'pegawai') {
            $query->whereExists(function ($queryExists) use ($penggunaAktif) {
                $queryExists->select(DB::raw(1))
                    ->from('d_st_tim')
                    ->whereColumn('d_st_tim.id_st', 'd_st.id_st')
                    ->where('d_st_tim.nip', $penggunaAktif->nip);
            });
        } elseif ($penggunaAktif->role === 'kabid' && $bidangPengguna) {
            $query->where('d_st.id_bidwas', $bidangPengguna);
        }

        $daftarSuratTugasBelumSelesai = $query->orderBy('end_date', 'asc')
            ->paginate(50, ['*'], 'overdue_page');

        return view('dashboard.partials.overdue_tasks', compact('daftarSuratTugasBelumSelesai'))->render();
    }

    public function daily(Request $request)
    {
        $tanggalTerpilih = $request->get('date', Carbon::today()->toDateString());
        $daftarPenugasan = $this->getDailyData($request)->paginate(25);
        return view('dashboard.daily', compact('daftarPenugasan', 'tanggalTerpilih'));
    }

    public function monthly(Request $request)
    {
        $bulanTerpilih = $request->get('month', Carbon::now()->month);
        $tahunTerpilih = $request->get('year', Carbon::now()->year);
        $tanggalMulai = $request->get('start_date');
        $tanggalSelesai = $request->get('end_date');
        $kataKunci = $request->get('search');
        $penggunaAktif = auth()->user();

        $isPimpinan = $penggunaAktif->role === 'pimpinan';
        $bidangTerkunci = !$isPimpinan;

        $bidangIdUser = $penggunaAktif->bidang_id
            ?: optional(DB::table('r_pegawai')->where('nip', $penggunaAktif->nip)->first('id_bidwas'))->id_bidwas;

        $idBidwas = $isPimpinan
            ? $request->get('bidwas')
            : $bidangIdUser;

        $rekapPenugasanBulanan = $this->getMonthlyData($request)->get();

        $daftarStStaff = collect();
        if ($penggunaAktif->role === 'pegawai') {
            $tMulai = $tanggalMulai ?: \Carbon\Carbon::create($tahunTerpilih, $bulanTerpilih, 1)->startOfMonth()->toDateString();
            $tAkhir = $tanggalSelesai ?: \Carbon\Carbon::create($tahunTerpilih, $bulanTerpilih, 1)->endOfMonth()->toDateString();
            $daftarStStaff = DB::table('d_st')
                ->join('d_st_tim', 'd_st.id_st', '=', 'd_st_tim.id_st')
                ->leftJoin('r_bidwas', 'd_st.id_bidwas', '=', 'r_bidwas.id_bidwas')
                ->where('d_st_tim.nip', $penggunaAktif->nip)
                ->where('d_st.start_date', '<=', $tAkhir)
                ->where('d_st.end_date', '>=', $tMulai)
                ->select('d_st.*', 'd_st_tim.peran', 'r_bidwas.nm_bidwas')
                ->orderBy('d_st.start_date', 'desc')
                ->get();
        }

        $daftarBidwas = DB::table('r_bidwas')->get()->map(function ($bidwas) {
            $bidwas->short_name = $bidwas->nm_bidwas;
            if (str_contains($bidwas->nm_bidwas, 'Instansi Pemerintah Pusat')) $bidwas->short_name = 'IPP';
            elseif (str_contains($bidwas->nm_bidwas, 'Pemerintah Daerah')) $bidwas->short_name = 'APD';
            elseif (str_contains($bidwas->nm_bidwas, 'Akuntan Negara')) $bidwas->short_name = 'AN';
            elseif (str_contains($bidwas->nm_bidwas, 'Investigasi')) $bidwas->short_name = 'Investigasi';
            elseif (str_contains($bidwas->nm_bidwas, 'Program dan Pelaporan')) $bidwas->short_name = 'P3A';
            elseif (str_contains($bidwas->nm_bidwas, 'Tata Usaha')) $bidwas->short_name = 'TU';
            elseif (str_contains($bidwas->nm_bidwas, 'Kepegawaian')) $bidwas->short_name = 'KEP';
            elseif (str_contains($bidwas->nm_bidwas, 'Keuangan')) $bidwas->short_name = 'KEU';
            elseif (str_contains($bidwas->nm_bidwas, 'Umum')) $bidwas->short_name = 'UMM';
            return $bidwas;
        });

        $daftarTahun = DB::table('d_st')
            ->select(DB::raw('YEAR(start_date) as year'))
            ->whereNotNull('start_date')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $bidwasTerpilih = $idBidwas ? $daftarBidwas->firstWhere('id_bidwas', (int) $idBidwas) : null;

        if ($bidangTerkunci && !$bidwasTerpilih && $penggunaAktif->bidang_id) {
            $bidwasTerpilih = $daftarBidwas->firstWhere('id_bidwas', (int) $penggunaAktif->bidang_id);
        }

        return view('dashboard.monthly', compact(
            'rekapPenugasanBulanan', 'bulanTerpilih', 'tahunTerpilih',
            'tanggalMulai', 'tanggalSelesai', 'daftarBidwas',
            'idBidwas', 'bidwasTerpilih', 'daftarTahun', 'kataKunci',
            'bidangTerkunci', 'daftarStStaff'
        ));
    }

    public function employeeDetail(Request $request, $nip)
    {
        $dataPegawai = DB::table('r_pegawai')->where('nip', $nip)->first();
        if (!$dataPegawai) abort(404);

        $bulanTerpilih = $request->get('month', Carbon::now()->month);
        $tahunTerpilih = $request->get('year', Carbon::now()->year);
        $tanggalMulai = $request->get('start_date');
        $tanggalSelesai = $request->get('end_date');

        $queryPenugasan = DB::table('d_st')
            ->join('d_st_tim', 'd_st.id_st', '=', 'd_st_tim.id_st')
            ->where('d_st_tim.nip', $nip)
            ->select('d_st.*', 'd_st_tim.peran');

        if ($tanggalMulai && $tanggalSelesai) {
            $queryPenugasan->where(function ($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->where('d_st.start_date', '<=', $tanggalSelesai)
                    ->where('d_st.end_date', '>=', $tanggalMulai);
            });
        } else {
            $hariPertamaBulanIni = Carbon::create($tahunTerpilih, $bulanTerpilih, 1)->startOfMonth()->toDateString();
            $hariTerakhirBulanIni = Carbon::create($tahunTerpilih, $bulanTerpilih, 1)->endOfMonth()->toDateString();
            
            $queryPenugasan->where(function ($query) use ($hariPertamaBulanIni, $hariTerakhirBulanIni) {
                $query->where('d_st.start_date', '<=', $hariTerakhirBulanIni)
                    ->where('d_st.end_date', '>=', $hariPertamaBulanIni);
            });
        }

        $idBidwas = $request->get('bidwas');
        $daftarPenugasan = $queryPenugasan->orderBy('d_st.start_date', 'desc')->get();

        return view('dashboard.employee_detail', compact('dataPegawai', 'daftarPenugasan', 'bulanTerpilih', 'tahunTerpilih', 'tanggalMulai', 'tanggalSelesai', 'idBidwas'));
    }

    public function bidwas()
    {
        $bidwasData = $this->getBidwasData();
        return view('dashboard.bidwas', compact('bidwasData'));
    }

    public function bidwasDetail(Request $request, $id)
    {
        $informasiBidang = DB::table('r_bidwas')->where('id_bidwas', $id)->first();
        if (!$informasiBidang) abort(404);

        $tanggalMulai = $request->get('start_date');
        $tanggalSelesai = $request->get('end_date');
        $kolomUrut = $request->get('sort', 'active_tasks');
        $arahUrut = $request->get('direction', 'desc');

        $daftarPegawai = $this->getBidwasDetailData($id, $tanggalMulai, $tanggalSelesai)
            ->orderBy($kolomUrut, $arahUrut)
            ->get();

        $today = Carbon::today()->toDateString();
        $totalSuratTugas = DB::table('d_st')->where('id_bidwas', $id)->count();
        $totalLaporanHasil = DB::table('d_lhp')
            ->join('d_st', 'd_lhp.id_st', '=', 'd_st.id_st')
            ->where('d_st.id_bidwas', $id)
            ->count();
            
        $querySuratTugasAktif = DB::table('d_st')
            ->where('id_bidwas', $id)
            ->whereIn('status_st', ['Realisasi', 'Perpanjangan ST']);
            
        if ($tanggalMulai && $tanggalSelesai) {
            $querySuratTugasAktif->where('start_date', '<=', $tanggalSelesai)
                         ->where('end_date', '>=', $tanggalMulai);
        } else {
            $querySuratTugasAktif->where('start_date', '<=', $today)
                         ->where('end_date', '>=', $today);
        }
        $totalSuratTugasAktif = $querySuratTugasAktif->count();

        return view('dashboard.bidwas_detail', compact('informasiBidang', 'daftarPegawai', 'totalSuratTugas', 'totalLaporanHasil', 'totalSuratTugasAktif', 'tanggalMulai', 'tanggalSelesai', 'kolomUrut', 'arahUrut'));
    }

    private function getBidwasDetailData($id, $tanggalMulai = null, $tanggalSelesai = null)
    {
        $queryDetailPegawai = DB::table('r_pegawai')
            ->where('r_pegawai.id_bidwas', $id)
            ->leftJoin('d_st_tim', 'r_pegawai.nip', '=', 'd_st_tim.nip')
            ->leftJoin('d_st', function ($join) use ($tanggalMulai, $tanggalSelesai) {
                $join->on('d_st_tim.id_st', '=', 'd_st.id_st');
                
                if ($tanggalMulai && $tanggalSelesai) {
                    $join->where('d_st.start_date', '<=', $tanggalSelesai)
                         ->where('d_st.end_date', '>=', $tanggalMulai);
                } else {
                    $today = Carbon::today()->toDateString();
                    $join->where('d_st.start_date', '<=', $today)
                         ->where('d_st.end_date', '>=', $today);
                }
                
                $join->whereIn('d_st.status_st', ['Realisasi', 'Perpanjangan ST']);
            });

        return $queryDetailPegawai->select(
                'r_pegawai.nip',
                'r_pegawai.nama',
                'r_pegawai.user_role',
                DB::raw('count(distinct d_st.id_st) as active_tasks'),
                DB::raw('count(distinct d_st_tim.id_st) as total_assignments')
            )
            ->groupBy('r_pegawai.id', 'r_pegawai.nip', 'r_pegawai.nama', 'r_pegawai.user_role');
    }

    public function exportBidwasDetailExcel(Request $request, $id)
    {
        $bidwas = DB::table('r_bidwas')->where('id_bidwas', $id)->first();
        if (!$bidwas) abort(404);

        $dataLaporan = $this->getBidwasDetailData($id, $request->get('start_date'), $request->get('end_date'))
            ->orderBy($request->get('sort', 'active_tasks'), $request->get('direction', 'desc'))
            ->get();

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\BidwasDetailExport($dataLaporan), 'Rekap_Pegawai_Bidang.xlsx');
    }

    public function exportBidwasDetailPdf(Request $request, $id)
    {
        $informasiBidang = DB::table('r_bidwas')->where('id_bidwas', $id)->first();
        if (!$informasiBidang) abort(404);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $daftarPegawai = $this->getBidwasDetailData($id, $startDate, $endDate)
            ->orderBy($request->get('sort', 'active_tasks'), $request->get('direction', 'desc'))
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('Exports.bidwas_detail_pdf', compact('daftarPegawai', 'informasiBidang', 'startDate', 'endDate'));
        return $pdf->download('Rekap_Pegawai_Bidang.pdf');
    }

    public function detail($id)
    {
        $suratTugas = DB::table('d_st')
            ->where('d_st.id_st', $id)
            ->first();

        if (!$suratTugas) abort(404);

        $lhpList = DB::table('d_lhp')->where('id_st', $id)->orderBy('tanggal_lhp', 'desc')->get();

        $timPenugasan = DB::table('d_st_tim')
            ->join('r_pegawai', 'd_st_tim.nip', '=', 'r_pegawai.nip')
            ->where('d_st_tim.id_st', $id)
            ->select('r_pegawai.nama', 'r_pegawai.nip', 'd_st_tim.peran')
            ->get();

        $user           = auth()->user();
        $bisaEditST     = $this->bisaEditST($suratTugas);
        $bisaHapusST    = $this->bisaHapusST();
        $bisaTambahLHP  = $this->bisaKelolaLHPBaru($suratTugas);
        $daftarPegawai  = ($bisaEditST) ? DB::table('r_pegawai')->orderBy('nama')->get(['nip', 'nama']) : collect();
        $daftarBidwas   = ($user->role === 'pimpinan') ? DB::table('r_bidwas')->orderBy('nm_bidwas')->get() : collect();

        if ($bisaEditST) {
            $daftarPegawai = $this->enrichDaftarPegawaiWithBeban($daftarPegawai);
        }

        return view('dashboard.detail', compact(
            'suratTugas', 'lhpList', 'timPenugasan',
            'bisaEditST', 'bisaHapusST', 'bisaTambahLHP',
            'daftarPegawai', 'daftarBidwas'
        ));
    }

    public function stList(Request $request)
    {
        $kataKunci = $request->get('search');
        $statusTerpilih = $request->get('status');
        $tanggalMulai = $request->get('start_date');
        $tanggalSelesai = $request->get('end_date');
        $penggunaAktif = auth()->user();

        $bidangPengguna = $this->bidangIdPengguna($penggunaAktif);
        $querySuratTugas = DB::table('d_st');

        if ($penggunaAktif->role === 'pegawai' || ($penggunaAktif->role === 'kabid' && !$bidangPengguna)) {
            $querySuratTugas->join('d_st_tim', 'd_st.id_st', '=', 'd_st_tim.id_st')
                ->where('d_st_tim.nip', $penggunaAktif->nip)
                ->select('d_st.*');
        } elseif ($penggunaAktif->role === 'kabid') {
            $querySuratTugas->where('d_st.id_bidwas', $bidangPengguna);
        }

        if ($kataKunci) {
            $querySuratTugas->where(function ($query) use ($kataKunci) {
                $query->where('d_st.nama_penugasan', 'like', "%{$kataKunci}%")
                    ->orWhere('d_st.no_surat_tugas', 'like', "%{$kataKunci}%");
            });
        }

        if ($statusTerpilih) {
            $querySuratTugas->where('d_st.status_st', $statusTerpilih);
        }

        if ($tanggalMulai && $tanggalSelesai) {
            $querySuratTugas->where(function ($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->whereBetween('d_st.start_date', [$tanggalMulai, $tanggalSelesai])
                    ->orWhereBetween('d_st.end_date', [$tanggalMulai, $tanggalSelesai])
                    ->orWhere(function ($subQuery) use ($tanggalMulai, $tanggalSelesai) {
                        $subQuery->where('d_st.start_date', '<=', $tanggalMulai)
                            ->where('d_st.end_date', '>=', $tanggalSelesai);
                    });
            });
        }

        $daftarSuratTugas = $querySuratTugas->orderBy('d_st.start_date', 'desc')->paginate(15);
        $daftarStatus = DB::table('d_st')
            ->distinct()
            ->whereNotNull('status_st')
            ->pluck('status_st')
            ->map(fn($status) => trim($status))
            ->unique()
            ->values();
        $daftarBidwas   = DB::table('r_bidwas')->orderBy('nm_bidwas')->get();
        $daftarPegawai  = $this->enrichDaftarPegawaiWithBeban(
            DB::table('r_pegawai')->orderBy('nama')->get(['nip', 'nama'])
        );
        $userBidangId   = auth()->user()->bidang_id;
        $maxStAktif     = self::MAX_ST_AKTIF;

        return view('dashboard.st', compact('daftarSuratTugas', 'kataKunci', 'statusTerpilih', 'daftarStatus', 'tanggalMulai', 'tanggalSelesai', 'daftarBidwas', 'daftarPegawai', 'userBidangId', 'maxStAktif'));
    }

    public function lhpList(Request $request)
    {
        $kataKunci = $request->get('search');
        $statusTerpilih = $request->get('status');
        $tanggalMulai = $request->get('start_date');
        $tanggalSelesai = $request->get('end_date');
        $penggunaAktif = auth()->user();

        $queryLhp = DB::table('d_lhp')
            ->leftJoin('d_st', 'd_lhp.id_st', '=', 'd_st.id_st');

        if ($penggunaAktif->role === 'pegawai') {
            $queryLhp->whereExists(function ($query) use ($penggunaAktif) {
                $query->select(DB::raw(1))
                    ->from('d_st_tim')
                    ->whereColumn('d_st_tim.id_st', 'd_st.id_st')
                    ->where('d_st_tim.nip', $penggunaAktif->nip);
            });
        }
        if ($kataKunci) {
            $queryLhp->where(function ($query) use ($kataKunci) {
                $query->where('d_lhp.nomor_lhp', 'like', "%{$kataKunci}%")
                    ->orWhere('d_st.nama_penugasan', 'like', "%{$kataKunci}%");
            });
        }

        if ($statusTerpilih) {
            $queryLhp->where('d_lhp.status_lhp', $statusTerpilih);
        }

        if ($tanggalMulai && $tanggalSelesai) {
            $queryLhp->whereBetween('d_lhp.tanggal_lhp', [$tanggalMulai, $tanggalSelesai]);
        }

        $daftarLhp = $queryLhp->select('d_lhp.*', 'd_st.nama_penugasan', 'd_st.id_st as st_id', 'd_st.no_surat_tugas', 'd_lhp.judul_lhp')
            ->orderBy('d_lhp.tanggal_lhp', 'desc')
            ->paginate(15);

        $daftarStatus = DB::table('d_lhp')
            ->distinct()
            ->whereNotNull('status_lhp')
            ->pluck('status_lhp')
            ->map(fn($status) => trim($status))
            ->unique()
            ->values();
        $daftarSt = DB::table('d_st')->orderBy('nama_penugasan')->get(['id_st', 'nama_penugasan', 'no_surat_tugas']);

        return view('dashboard.lhp', compact('daftarLhp', 'kataKunci', 'statusTerpilih', 'daftarStatus', 'tanggalMulai', 'tanggalSelesai', 'daftarSt'));
    }

    private function bisaKelolaSTBaru(): bool
    {
        return in_array(auth()->user()->role, ['pimpinan', 'kabid']);
    }

    private function bisaEditST($st): bool
    {
        $user = auth()->user();
        if ($user->role === 'pimpinan') return true;
        if ($user->role === 'kabid') return $st->id_bidwas == $user->bidang_id;
        return false;
    }

    private function bisaHapusST(): bool
    {
        return auth()->user()->role === 'pimpinan';
    }

    private function bisaKelolaLHPBaru($st = null): bool
    {
        $user = auth()->user();
        if ($user->role === 'pimpinan') return true;
        if ($user->role === 'kabid') {
            if (!$st) return true;
            return $st->id_bidwas == $user->bidang_id;
        }
        return false;
    }

    private function bisaEditLHP($lhp): bool
    {
        $user = auth()->user();
        if ($user->role === 'pimpinan') return true;
        if ($user->role === 'kabid') {
            $st = DB::table('d_st')->where('id_st', $lhp->id_st)->first();
            return $st && $st->id_bidwas == $user->bidang_id;
        }
        return false;
    }

    private function bisaHapusLHP(): bool
    {
        return auth()->user()->role === 'pimpinan';
    }

    /**
     * Tambahkan field active_st_count ke setiap pegawai dalam koleksi.
     */
    private function enrichDaftarPegawaiWithBeban($daftarPegawai)
    {
        $nipList = $daftarPegawai->pluck('nip')->toArray();
        if (empty($nipList)) return $daftarPegawai;

        $beban = DB::table('d_st_tim')
            ->join('d_st', 'd_st_tim.id_st', '=', 'd_st.id_st')
            ->whereIn('d_st_tim.nip', $nipList)
            ->whereIn('d_st.status_st', ['Realisasi', 'Perpanjangan ST'])
            ->select('d_st_tim.nip', DB::raw('count(d_st.id_st) as jumlah'))
            ->groupBy('d_st_tim.nip')
            ->pluck('jumlah', 'nip');

        return $daftarPegawai->map(function ($p) use ($beban) {
            $p->active_st_count = $beban[$p->nip] ?? 0;
            return $p;
        });
    }

    /**
     * Kembalikan daftar pegawai (dari $nipList) yang sudah mencapai batas MAX_ST_AKTIF.
     * $kecualiIdSt: lewati ST ini saat menghitung (dipakai saat update).
     */
    private function pegawaiMelebihiBebanST(array $nipList, ?int $kecualiIdSt = null): array
    {
        $q = DB::table('d_st_tim')
            ->join('d_st', 'd_st_tim.id_st', '=', 'd_st.id_st')
            ->join('r_pegawai', 'd_st_tim.nip', '=', 'r_pegawai.nip')
            ->whereIn('d_st_tim.nip', $nipList)
            ->whereIn('d_st.status_st', ['Realisasi', 'Perpanjangan ST']);

        if ($kecualiIdSt !== null) {
            $q->where('d_st_tim.id_st', '!=', $kecualiIdSt);
        }

        return $q->select('r_pegawai.nama', 'd_st_tim.nip', DB::raw('count(d_st.id_st) as jumlah'))
            ->groupBy('r_pegawai.nama', 'd_st_tim.nip')
            ->having('jumlah', '>=', self::MAX_ST_AKTIF)
            ->get()
            ->toArray();
    }

    public function stStore(Request $request)
    {
        abort_unless($this->bisaKelolaSTBaru(), 403);

        $request->validate([
            'nama_penugasan' => 'required|string|max:500',
            'no_surat_tugas' => 'nullable|string|max:100',
            'id_bidwas'      => 'required|integer',
            'status_st'      => 'required|string|max:25',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
        ]);

        $user = auth()->user();
        $idBidwas = ($user->role === 'kabid') ? $user->bidang_id : $request->id_bidwas;
        $idBaru = (DB::table('d_st')->max('id_st') ?? 0) + 1;

        DB::table('d_st')->insert([
            'id_topik'       => 0,
            'id_unit'        => 47,
            'id_bidwas'      => $idBidwas,
            'id_st'          => $idBaru,
            'nama_penugasan' => $request->nama_penugasan,
            'no_surat_tugas' => $request->no_surat_tugas,
            'status_st'      => $request->status_st,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
        ]);

        // Simpan anggota tim
        if ($request->has('nip') && is_array($request->nip)) {
            $nipList = array_values(array_filter((array) $request->nip));
            if (!empty($nipList)) {
                $melebihi = $this->pegawaiMelebihiBebanST($nipList);
                if (!empty($melebihi)) {
                    // Hapus ST yang terlanjur dibuat agar tidak ada data orphan
                    DB::table('d_st')->where('id_st', $idBaru)->delete();
                    $namaPegawai = collect($melebihi)->map(fn($p) => $p->nama . ' (' . $p->jumlah . ' ST aktif)')->implode('; ');
                    return back()->withInput()->withErrors(['nip' => 'Pegawai berikut sudah mencapai batas ' . self::MAX_ST_AKTIF . ' ST aktif: ' . $namaPegawai]);
                }
            }
            $timRows = [];
            foreach ($request->nip as $i => $nip) {
                if ($nip) {
                    $timRows[] = [
                        'id_st' => $idBaru,
                        'nip'   => $nip,
                        'peran' => $request->peran[$i] ?? 'Anggota',
                    ];
                }
            }
            if ($timRows) DB::table('d_st_tim')->insert($timRows);
        }

        return redirect()->route('dashboard.st')->with('success', 'Surat Tugas berhasil ditambahkan.');
    }

    public function stUpdate(Request $request, $id)
    {
        $st = DB::table('d_st')->where('id_st', $id)->first();
        abort_unless($st && $this->bisaEditST($st), 403);

        $request->validate([
            'nama_penugasan' => 'required|string|max:500',
            'no_surat_tugas' => 'nullable|string|max:100',
            'id_bidwas'      => 'required|integer',
            'status_st'      => 'required|string|max:25',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
        ]);

        $user = auth()->user();
        $idBidwas = ($user->role === 'kabid') ? $user->bidang_id : $request->id_bidwas;

        DB::table('d_st')->where('id_st', $id)->update([
            'id_bidwas'      => $idBidwas,
            'nama_penugasan' => $request->nama_penugasan,
            'no_surat_tugas' => $request->no_surat_tugas,
            'status_st'      => $request->status_st,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
        ]);

        // Perbarui anggota tim jika dikirim
        if ($request->has('nip')) {
            $nipList = array_values(array_filter((array) $request->nip));
            if (!empty($nipList)) {
                // Kecualikan ST ini sendiri dari hitungan agar pengecekan akurat
                $melebihi = $this->pegawaiMelebihiBebanST($nipList, (int) $id);
                if (!empty($melebihi)) {
                    $namaPegawai = collect($melebihi)->map(fn($p) => $p->nama . ' (' . $p->jumlah . ' ST aktif)')->implode('; ');
                    return back()->withInput()->withErrors(['nip' => 'Pegawai berikut sudah mencapai batas ' . self::MAX_ST_AKTIF . ' ST aktif: ' . $namaPegawai]);
                }
            }
            DB::table('d_st_tim')->where('id_st', $id)->delete();
            $timRows = [];
            foreach ((array) $request->nip as $i => $nip) {
                if ($nip) {
                    $timRows[] = [
                        'id_st' => $id,
                        'nip'   => $nip,
                        'peran' => $request->peran[$i] ?? 'Anggota',
                    ];
                }
            }
            if ($timRows) DB::table('d_st_tim')->insert($timRows);
        }

        $redirect = $request->get('redirect_to') === 'detail'
            ? route('st.detail', $id)
            : route('dashboard.st');

        return redirect($redirect)->with('success', 'Surat Tugas berhasil diperbarui.');
    }

    public function stDestroy($id)
    {
        abort_unless($this->bisaHapusST(), 403);
        DB::table('d_st_tim')->where('id_st', $id)->delete();
        DB::table('d_st')->where('id_st', $id)->delete();
        return redirect()->route('dashboard.st')->with('success', 'Surat Tugas berhasil dihapus.');
    }

    public function lhpStore(Request $request)
    {
        $st = $request->id_st ? DB::table('d_st')->where('id_st', $request->id_st)->first() : null;
        abort_unless($this->bisaKelolaLHPBaru($st), 403);

        $request->validate([
            'id_st'       => 'required|integer',
            'nomor_lhp'   => 'nullable|string|max:120',
            'judul_lhp'   => 'nullable|string',
            'tanggal_lhp' => 'required|date',
            'status_lhp'  => 'required|string|max:100',
        ]);

        if (!$st) return back()->withErrors(['id_st' => 'Surat Tugas tidak ditemukan.']);

        $idBaru = (DB::table('d_lhp')->max('id_lhp') ?? 0) + 1;

        DB::table('d_lhp')->insert([
            'id_topik'          => $st->id_topik ?? 0,
            'id_unit'           => $st->id_unit ?? 47,
            'id_bidwas'         => $st->id_bidwas,
            'id_st'             => $request->id_st,
            'id_lhp'            => $idBaru,
            'judul_lhp'         => $request->judul_lhp,
            'nomor_lhp'         => $request->nomor_lhp,
            'tanggal_lhp'       => $request->tanggal_lhp,
            'status_lhp'        => $request->status_lhp,
            'status_dl'         => $request->status_lhp,
            'tidak_perlu_esign' => 0,
        ]);

        $redirect = $request->get('redirect_to') === 'detail'
            ? route('st.detail', $request->id_st)
            : route('dashboard.lhp');

        return redirect($redirect)->with('success', 'LHP berhasil ditambahkan.');
    }

    public function lhpUpdate(Request $request, $id)
    {
        $lhp = DB::table('d_lhp')->where('id_lhp', $id)->first();
        abort_unless($lhp && $this->bisaEditLHP($lhp), 403);

        $request->validate([
            'id_st'       => 'required|integer',
            'nomor_lhp'   => 'nullable|string|max:120',
            'judul_lhp'   => 'nullable|string',
            'tanggal_lhp' => 'required|date',
            'status_lhp'  => 'required|string|max:100',
        ]);

        $st = DB::table('d_st')->where('id_st', $request->id_st)->first();

        DB::table('d_lhp')->where('id_lhp', $id)->update([
            'id_st'       => $request->id_st,
            'id_bidwas'   => $st ? $st->id_bidwas : $lhp->id_bidwas,
            'judul_lhp'   => $request->judul_lhp,
            'nomor_lhp'   => $request->nomor_lhp,
            'tanggal_lhp' => $request->tanggal_lhp,
            'status_lhp'  => $request->status_lhp,
            'status_dl'   => $request->status_lhp,
        ]);

        return redirect()->route('dashboard.lhp')->with('success', 'LHP berhasil diperbarui.');
    }

    public function lhpDestroy(Request $request, $id)
    {
        abort_unless($this->bisaHapusLHP(), 403);
        $lhp = DB::table('d_lhp')->where('id_lhp', $id)->first();
        DB::table('d_lhp')->where('id_lhp', $id)->delete();

        if ($request->get('redirect_to') === 'detail' && $lhp) {
            return redirect()->route('st.detail', $lhp->id_st)->with('success', 'LHP berhasil dihapus.');
        }
        return redirect()->route('dashboard.lhp')->with('success', 'LHP berhasil dihapus.');
    }

    public function exportStExcel(Request $request)
    {
        $dataLaporan = $this->getStData($request);
        return Excel::download(new StExport($dataLaporan), 'Daftar_Surat_Tugas.xlsx');
    }

    public function exportStPdf(Request $request)
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(0);
        $daftarSuratTugas = $this->getStData($request);
        $berkasPdf = Pdf::loadView('Exports.st_pdf', compact('daftarSuratTugas'));
        return $berkasPdf->download('Daftar_Surat_Tugas.pdf');
    }

    public function exportLhpExcel(Request $request)
    {
        $dataLaporan = $this->getLhpData($request);
        return Excel::download(new LhpExport($dataLaporan), 'Daftar_LHP.xlsx');
    }

    public function exportLhpPdf(Request $request)
    {
        ini_set('memory_limit', '2048M');
        set_time_limit(0);
        $daftarLhp = $this->getLhpData($request);
        $berkasPdf = Pdf::loadView('Exports.lhp_pdf', compact('daftarLhp'));
        return $berkasPdf->download('Daftar_LHP.pdf');
    }

    public function exportBidwasExcel()
    {
        $dataLaporan = $this->getBidwasData();
        return Excel::download(new BidwasExport($dataLaporan), 'Rekap_Capaian_Bidang.xlsx');
    }

    public function exportBidwasPdf()
    {
        $rekapCapaianBidang = $this->getBidwasData();
        $berkasPdf = Pdf::loadView('Exports.bidwas_pdf', compact('rekapCapaianBidang'));
        return $berkasPdf->download('Rekap_Capaian_Bidang.pdf');
    }

    public function exportDailyExcel(Request $request)
    {
        $dataLaporan = $this->getDailyData($request)->get();
        return Excel::download(new DailyExport($dataLaporan), 'Monitoring_Harian_' . $request->get('date') . '.xlsx');
    }

    public function exportDailyPdf(Request $request)
    {
        $tanggalLaporan = $request->get('date', Carbon::today()->toDateString());
        $daftarAktivitasHarian = $this->getDailyData($request)->get();
        $berkasPdf = Pdf::loadView('Exports.daily_pdf', compact('daftarAktivitasHarian', 'tanggalLaporan'));
        return $berkasPdf->download('Monitoring_Harian_' . $tanggalLaporan . '.pdf');
    }

    public function exportMonthlyExcel(Request $request)
    {
        $dataLaporan = $this->getMonthlyData($request)->get();
        return Excel::download(new MonthlyExport($dataLaporan), 'Rekap_Penugasan_Pegawai.xlsx');
    }

    public function exportMonthlyPdf(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        // Jika user tidak pilih rentang, pakai bulan & tahun terpilih
        if (!$startDate || !$endDate) {
            $bulan = $request->get('month', Carbon::now()->month);
            $tahun = $request->get('year', Carbon::now()->year);
            $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth()->toDateString();
            $endDate   = Carbon::create($tahun, $bulan, 1)->endOfMonth()->toDateString();
        }

        $penggunaAktif = auth()->user();
        $isPimpinan = $penggunaAktif->role === 'pimpinan';

        // Tentukan divisi/bidang yang sedang di-export (untuk ditampilkan di PDF)
        if ($isPimpinan) {
            $idBidwasExport = $request->get('bidwas'); // null = semua
        } else {
            $idBidwasExport = $penggunaAktif->bidang_id
                ?: optional(DB::table('r_pegawai')->where('nip', $penggunaAktif->nip)->first('id_bidwas'))->id_bidwas;
        }
        $bidangLabel = $idBidwasExport
            ? (DB::table('r_bidwas')->where('id_bidwas', $idBidwasExport)->value('nm_bidwas') ?? '-')
            : 'Semua Bidang';

        // Rekap (ringkasan per pegawai) hanya untuk yang mengawasi grup:
        //   pimpinan (semua/pilihan) & korwas (punya bidang).
        $modeRekap = $isPimpinan || ($penggunaAktif->role === 'kabid' && $idBidwasExport);

        if ($modeRekap) {
            $rekapPenugasan   = $this->getMonthlyData($request)->get();
            $rincianPenugasan = collect();
        } else {
            $rincianPenugasan = $this->getMonthlyDetailData($request)->get();
            $rekapPenugasan   = collect();
        }

        $berkasPdf = Pdf::loadView('Exports.monthly_pdf', compact(
            'modeRekap', 'rekapPenugasan', 'rincianPenugasan', 'startDate', 'endDate', 'bidangLabel'
        ));
        return $berkasPdf->download('Rekap_Penugasan_Pegawai.pdf');
    }

    private function getBidwasData()
    {
        $rekapBidang = DB::table('r_bidwas')
            ->leftJoin('d_st', 'r_bidwas.id_bidwas', '=', 'd_st.id_bidwas')
            ->leftJoin('d_lhp', 'd_st.id_st', '=', 'd_lhp.id_st')
            ->select(
                'r_bidwas.id_bidwas',
                'r_bidwas.nm_bidwas as nama_bidwas',
                'r_bidwas.kd_bidwas as kode',
                DB::raw('count(distinct d_st.id_st) as total_st'),
                DB::raw('count(distinct d_lhp.id_lhp) as total_lhp')
            )
            ->groupBy('r_bidwas.id_bidwas', 'r_bidwas.nm_bidwas', 'r_bidwas.kd_bidwas')
            ->get();

        $jumlahPegawaiPerBidang = DB::table('r_pegawai')
            ->select('id_bidwas', DB::raw('count(*) as total_pegawai'))
            ->whereNotNull('id_bidwas')
            ->groupBy('id_bidwas')
            ->pluck('total_pegawai', 'id_bidwas');

        foreach ($rekapBidang as $dataBidang) {
            $dataBidang->total_pegawai = $jumlahPegawaiPerBidang[$dataBidang->id_bidwas] ?? 0;
        }

        return $rekapBidang;
    }

    private function getDailyData(Request $request)
    {
        $tanggalTerpilih = $request->get('date', Carbon::today()->toDateString());
        $penggunaAktif = auth()->user();

        $kueriPenugasan = DB::table('d_st')
            ->join('d_st_tim', 'd_st.id_st', '=', 'd_st_tim.id_st')
            ->join('r_pegawai', 'd_st_tim.nip', '=', 'r_pegawai.nip')
            ->where('d_st.start_date', '<=', $tanggalTerpilih)
            ->where('d_st.end_date', '>=', $tanggalTerpilih)
            ->whereIn('d_st.status_st', ['Realisasi', 'Perpanjangan ST']);

        // pegawai & kabid (korwas) hanya lihat penugasan sendiri di harian
        if (in_array($penggunaAktif->role, ['pegawai', 'kabid'])) {
            $kueriPenugasan->where('d_st_tim.nip', $penggunaAktif->nip);
        }

        return $kueriPenugasan->select('r_pegawai.nama', 'r_pegawai.nip', 'd_st.nama_penugasan as st_nama', 'd_st.no_surat_tugas', 'd_st.start_date', 'd_st.end_date', 'd_st.status_st', 'd_st_tim.peran', 'd_st.id_st');
    }

    private function getMonthlyData(Request $request)
    {
        $bulanTerpilih = $request->get('month', Carbon::now()->month);
        $tahunTerpilih = $request->get('year', Carbon::now()->year);
        $tanggalMulai = $request->get('start_date');
        $tanggalSelesai = $request->get('end_date');
        $kataKunci = $request->get('search');
        $penggunaAktif = auth()->user();

        // Tentukan filter bidang berdasarkan role
        // pimpinan → bebas pilih dari request; kabid/pegawai → terkunci ke bidang sendiri
        $isPimpinan = $penggunaAktif->role === 'pimpinan';

        if (!$isPimpinan) {
            $idBidwas = $penggunaAktif->bidang_id
                ?: optional(DB::table('r_pegawai')->where('nip', $penggunaAktif->nip)->first('id_bidwas'))->id_bidwas;
        } else {
            $idBidwas = $request->get('bidwas');
        }

        $kueriBebanKerjaBulanan = DB::table('r_pegawai')
            ->leftJoin('d_st_tim', 'r_pegawai.nip', '=', 'd_st_tim.nip')
            ->leftJoin('d_st', 'd_st_tim.id_st', '=', 'd_st.id_st');

        // Filter data berdasarkan role
        if ($penggunaAktif->role === 'pegawai') {
            // Staff hanya lihat dirinya sendiri
            $kueriBebanKerjaBulanan->where('r_pegawai.nip', $penggunaAktif->nip);
        } elseif ($isPimpinan) {
            if ($idBidwas) {
                $kueriBebanKerjaBulanan->where('r_pegawai.id_bidwas', $idBidwas);
            }
        } elseif ($idBidwas) {
            $kueriBebanKerjaBulanan->where('r_pegawai.id_bidwas', $idBidwas);
        } else {
            // Kabid tanpa bidang (subkoor) → hanya dirinya sendiri (cegah lihat semua)
            $kueriBebanKerjaBulanan->where('r_pegawai.nip', $penggunaAktif->nip);
        }

        if ($kataKunci) {
            $kueriBebanKerjaBulanan->where(function ($query) use ($kataKunci) {
                $query->where('r_pegawai.nama', 'like', "%{$kataKunci}%")
                    ->orWhere('r_pegawai.nip', 'like', "%{$kataKunci}%");
            });
        }

        if ($tanggalMulai && $tanggalSelesai) {
            $kueriBebanKerjaBulanan->where(function ($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->where('d_st.start_date', '<=', $tanggalSelesai)
                    ->where('d_st.end_date', '>=', $tanggalMulai);
            });
        } else {
            $hariPertamaBulan = Carbon::create($tahunTerpilih, $bulanTerpilih, 1)->startOfMonth()->toDateString();
            $hariTerakhirBulan = Carbon::create($tahunTerpilih, $bulanTerpilih, 1)->endOfMonth()->toDateString();

            $kueriBebanKerjaBulanan->where(function ($query) use ($hariPertamaBulan, $hariTerakhirBulan) {
                $query->where('d_st.start_date', '<=', $hariTerakhirBulan)
                    ->where('d_st.end_date', '>=', $hariPertamaBulan);
            });
        }

        return $kueriBebanKerjaBulanan->select('r_pegawai.nama', 'r_pegawai.nip', DB::raw('count(d_st.id_st) as total'))
            ->groupBy('r_pegawai.id', 'r_pegawai.nama', 'r_pegawai.nip')
            ->orderBy('total', 'desc');
    }

    /**
     * Daftar surat tugas per pegawai untuk periode terpilih (untuk export rincian bulanan).
     * Mengikuti aturan role yang sama dengan getMonthlyData.
     */
    private function getMonthlyDetailData(Request $request)
    {
        $bulanTerpilih  = $request->get('month', Carbon::now()->month);
        $tahunTerpilih  = $request->get('year', Carbon::now()->year);
        $tanggalMulai   = $request->get('start_date');
        $tanggalSelesai = $request->get('end_date');
        $kataKunci      = $request->get('search');
        $penggunaAktif  = auth()->user();

        $isPimpinan = $penggunaAktif->role === 'pimpinan';
        if (!$isPimpinan) {
            $idBidwas = $penggunaAktif->bidang_id
                ?: optional(DB::table('r_pegawai')->where('nip', $penggunaAktif->nip)->first('id_bidwas'))->id_bidwas;
        } else {
            $idBidwas = $request->get('bidwas');
        }

        $query = DB::table('r_pegawai')
            ->join('d_st_tim', 'r_pegawai.nip', '=', 'd_st_tim.nip')
            ->join('d_st', 'd_st_tim.id_st', '=', 'd_st.id_st');

        if ($penggunaAktif->role === 'pegawai') {
            $query->where('r_pegawai.nip', $penggunaAktif->nip);
        } elseif ($isPimpinan) {
            if ($idBidwas) {
                $query->where('r_pegawai.id_bidwas', $idBidwas);
            }
        } elseif ($idBidwas) {
            $query->where('r_pegawai.id_bidwas', $idBidwas);
        } else {
            // Kabid tanpa bidang (subkoor) → hanya dirinya sendiri
            $query->where('r_pegawai.nip', $penggunaAktif->nip);
        }

        if ($kataKunci) {
            $query->where(function ($q) use ($kataKunci) {
                $q->where('r_pegawai.nama', 'like', "%{$kataKunci}%")
                  ->orWhere('r_pegawai.nip', 'like', "%{$kataKunci}%");
            });
        }

        if ($tanggalMulai && $tanggalSelesai) {
            $query->where('d_st.start_date', '<=', $tanggalSelesai)
                  ->where('d_st.end_date', '>=', $tanggalMulai);
        } else {
            $awal  = Carbon::create($tahunTerpilih, $bulanTerpilih, 1)->startOfMonth()->toDateString();
            $akhir = Carbon::create($tahunTerpilih, $bulanTerpilih, 1)->endOfMonth()->toDateString();
            $query->where('d_st.start_date', '<=', $akhir)
                  ->where('d_st.end_date', '>=', $awal);
        }

        return $query->select(
                'r_pegawai.nama',
                'r_pegawai.nip',
                'd_st.no_surat_tugas',
                'd_st.nama_penugasan',
                'd_st.start_date',
                'd_st.end_date',
                'd_st.status_st',
                'd_st_tim.peran'
            )
            ->orderBy('r_pegawai.nama')
            ->orderBy('d_st.start_date');
    }

    private function getStData(Request $request)
    {
        $kataKunci = $request->get('search');
        $statusTerpilih = $request->get('status');
        $tanggalMulai = $request->get('start_date');
        $tanggalSelesai = $request->get('end_date');
        $penggunaAktif = auth()->user();

        $kueriDataSuratTugas = DB::table('d_st')
            ->leftJoin('r_bidwas', 'd_st.id_bidwas', '=', 'r_bidwas.id_bidwas')
            ->select(
                'd_st.id_st',
                'd_st.no_surat_tugas as no_st',
                'd_st.nama_penugasan as nama',
                'r_bidwas.nm_bidwas',
                'd_st.start_date',
                'd_st.end_date',
                'd_st.status_st as status'
            );

        $bidangPengguna = $this->bidangIdPengguna($penggunaAktif);
        if ($penggunaAktif->role === 'pegawai' || ($penggunaAktif->role === 'kabid' && !$bidangPengguna)) {
            $kueriDataSuratTugas->join('d_st_tim', 'd_st.id_st', '=', 'd_st_tim.id_st')
                ->where('d_st_tim.nip', $penggunaAktif->nip);
        } elseif ($penggunaAktif->role === 'kabid') {
            $kueriDataSuratTugas->where('d_st.id_bidwas', $bidangPengguna);
        }

        if ($kataKunci) {
            $kueriDataSuratTugas->where(function ($query) use ($kataKunci) {
                $query->where('d_st.nama_penugasan', 'like', "%{$kataKunci}%")
                    ->orWhere('d_st.no_surat_tugas', 'like', "%{$kataKunci}%");
            });
        }

        if ($statusTerpilih) {
            $kueriDataSuratTugas->where('d_st.status_st', $statusTerpilih);
        }

        if ($tanggalMulai && $tanggalSelesai) {
            $kueriDataSuratTugas->where(function ($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->where('d_st.start_date', '<=', $tanggalSelesai)
                  ->where('d_st.end_date', '>=', $tanggalMulai);
            });
        }

        return $kueriDataSuratTugas->get();
    }

    private function getLhpData(Request $request)
    {
        $kataKunci = $request->get('search');
        $statusTerpilih = $request->get('status');
        $tanggalMulai = $request->get('start_date');
        $tanggalSelesai = $request->get('end_date');
        $penggunaAktif = auth()->user();

        $kueriDataLhp = DB::table('d_lhp')
            ->leftJoin('d_st', 'd_lhp.id_st', '=', 'd_st.id_st')
            ->leftJoin('r_bidwas', 'd_st.id_bidwas', '=', 'r_bidwas.id_bidwas')
            ->select(
                'd_lhp.id_lhp',
                'd_lhp.nomor_lhp as no_lhp',
                'd_st.no_surat_tugas as no_st',
                'd_st.nama_penugasan as nama',
                'r_bidwas.nm_bidwas',
                'd_lhp.tanggal_lhp as tgl_lhp',
                'd_lhp.status_lhp as status'
            );

        if ($penggunaAktif->role === 'pegawai') {
            $kueriDataLhp->whereExists(function ($query) use ($penggunaAktif) {
                $query->select(DB::raw(1))
                    ->from('d_st_tim')
                    ->whereColumn('d_st_tim.id_st', 'd_st.id_st')
                    ->where('d_st_tim.nip', $penggunaAktif->nip);
            });
        }

        if ($kataKunci) {
            $kueriDataLhp->where(function ($query) use ($kataKunci) {
                $query->where('d_lhp.nomor_lhp', 'like', "%{$kataKunci}%")
                    ->orWhere('d_st.nama_penugasan', 'like', "%{$kataKunci}%");
            });
        }

        if ($statusTerpilih) {
            $kueriDataLhp->where('d_lhp.status_lhp', $statusTerpilih);
        }

        if ($tanggalMulai && $tanggalSelesai) {
            $kueriDataLhp->where(function ($query) use ($tanggalMulai, $tanggalSelesai) {
                $query->where('d_lhp.tanggal_lhp', '<=', $tanggalSelesai)
                  ->where('d_lhp.tanggal_lhp', '>=', $tanggalMulai);
            });
        }

        return $kueriDataLhp->get();
    }
}
