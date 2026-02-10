<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $thisYear = Carbon::now()->year;
        $user = auth()->user();

        $queryST = DB::table('d_st')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereIn('status_st', ['Realisasi', 'Perpanjangan ST']);

        if ($user->role === 'pegawai') {
            $queryST->whereExists(function ($q) use ($user) {
                $q->select(DB::raw(1))
                    ->from('d_st_tim')
                    ->whereColumn('d_st_tim.id_st', 'd_st.id_st')
                    ->where('d_st_tim.nip', $user->nip);
            });
        }
        $activeST = $queryST->count();

        $activeTasksQuery = DB::table('d_st_tim')
            ->join('d_st', 'd_st_tim.id_st', '=', 'd_st.id_st')
            ->join('r_pegawai', 'd_st_tim.nip', '=', 'r_pegawai.nip')
            ->where('d_st.start_date', '<=', $today)
            ->where('d_st.end_date', '>=', $today)
            ->whereIn('d_st.status_st', ['Realisasi', 'Perpanjangan ST']);

        if ($user->role === 'pegawai') {
            $activeTasksQuery->where('d_st_tim.nip', $user->nip);
        }

        $topBusyEmployees = (clone $activeTasksQuery)
            ->select('r_pegawai.nama', 'r_pegawai.nip', DB::raw('count(d_st_tim.id_st) as active_tasks'))
            ->groupBy('r_pegawai.nama', 'r_pegawai.nip')
            ->orderByDesc('active_tasks')
            ->limit(5)
            ->get();

        $busyNips = (clone $activeTasksQuery)->pluck('d_st_tim.nip');

        $availablePegawaiQuery = DB::table('r_pegawai')
            ->whereNotIn('nip', $busyNips);

        if ($user->role === 'pegawai') {
            $availablePegawaiQuery->where('nip', $user->nip);
        }

        $leastBusyEmployees = $availablePegawaiQuery
            ->select('nama', 'nip')
            ->limit(10)
            ->get();

        $unfinishedQuery = DB::table('d_st')
            ->where('end_date', '<', $today)
            ->whereNotIn('status_st', ['Selesai', 'Batal']);

        if ($user->role === 'pegawai') {
            $unfinishedQuery->whereExists(function ($q) use ($user) {
                $q->select(DB::raw(1))
                    ->from('d_st_tim')
                    ->whereColumn('d_st_tim.id_st', 'd_st.id_st')
                    ->where('d_st_tim.nip', $user->nip);
            });
        }

        $unfinishedSTs = (clone $unfinishedQuery)->take(5)->get();
        $totalUnfinished = (clone $unfinishedQuery)->count();

        $bidwasStats = DB::table('r_bidwas')
            ->leftJoin('d_st', 'r_bidwas.id_bidwas', '=', 'd_st.id_bidwas')
            ->select('r_bidwas.kd_bidwas as kode', 'r_bidwas.nm_bidwas as nama', DB::raw('count(d_st.id_st) as total'))
            ->groupBy('r_bidwas.id_bidwas', 'r_bidwas.kd_bidwas', 'r_bidwas.nm_bidwas')
            ->get();

        $monthlyTrend = DB::table('d_st')
            ->select(DB::raw('MONTH(start_date) as month'), DB::raw('count(*) as total'))
            ->whereYear('start_date', $thisYear);

        if ($user->role === 'pegawai') {
            $monthlyTrend->whereExists(function ($q) use ($user) {
                $q->select(DB::raw(1))
                    ->from('d_st_tim')
                    ->whereColumn('d_st_tim.id_st', 'd_st.id_st')
                    ->where('d_st_tim.nip', $user->nip);
            });
        }
        $monthlyTrend = $monthlyTrend->groupBy('month')->orderBy('month')->get();

        $statusDist = DB::table('d_st')
            ->select('status_st', DB::raw('count(*) as total'));

        if ($user->role === 'pegawai') {
            $statusDist->whereExists(function ($q) use ($user) {
                $q->select(DB::raw(1))
                    ->from('d_st_tim')
                    ->whereColumn('d_st_tim.id_st', 'd_st.id_st')
                    ->where('d_st_tim.nip', $user->nip);
            });
        }
        $statusDist = $statusDist->groupBy('status_st')->get();

        $totalPegawai = DB::table('r_pegawai')->count();
        $totalLHP = DB::table('d_lhp')->count();
        $targetPKPT = DB::table('r_pkpt')->count();

        return view('dashboard.index', compact(
            'activeST',
            'totalPegawai',
            'totalLHP',
            'targetPKPT',
            'topBusyEmployees',
            'leastBusyEmployees',
            'unfinishedSTs',
            'totalUnfinished',
            'bidwasStats',
            'monthlyTrend',
            'statusDist'
        ));
    }

    public function daily(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $user = auth()->user();

        $query = DB::table('d_st')
            ->join('d_st_tim', 'd_st.id_st', '=', 'd_st_tim.id_st')
            ->join('r_pegawai', 'd_st_tim.nip', '=', 'r_pegawai.nip')
            ->where('d_st.start_date', '<=', $date)
            ->where('d_st.end_date', '>=', $date)
            ->whereIn('d_st.status_st', ['Realisasi', 'Perpanjangan ST']);

        if ($user->role === 'pegawai') {
            $query->where('d_st_tim.nip', $user->nip);
        }

        $penugasan = $query->select('r_pegawai.nama', 'r_pegawai.nip', 'd_st.nama_penugasan as st_nama', 'd_st.start_date', 'd_st.end_date', 'd_st_tim.peran', 'd_st.id_st')
            ->paginate(25);

        return view('dashboard.daily', compact('penugasan', 'date'));
    }

    public function monthly(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $idBidwas = $request->get('bidwas');
        $search = $request->get('search');

        $user = auth()->user();
        $query = DB::table('r_pegawai')
            ->leftJoin('d_st_tim', 'r_pegawai.nip', '=', 'd_st_tim.nip')
            ->leftJoin('d_st', function ($join) {
                $join->on('d_st_tim.id_st', '=', 'd_st.id_st');
            });

        if ($user->role === 'pegawai') {
            $query->where('r_pegawai.nip', $user->nip);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('r_pegawai.nama', 'like', "%{$search}%")
                    ->orWhere('r_pegawai.nip', 'like', "%{$search}%");
            });
        }

        if ($startDate && $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('d_st.start_date', [$startDate, $endDate])
                    ->orWhereBetween('d_st.end_date', [$startDate, $endDate])
                    ->orWhere(function ($sq) use ($startDate, $endDate) {
                        $sq->where('d_st.start_date', '<=', $startDate)
                            ->where('d_st.end_date', '>=', $endDate);
                    });
            });
        } else {
            $query->whereMonth('d_st.start_date', '<=', $month)
                ->whereMonth('d_st.end_date', '>=', $month)
                ->whereYear('d_st.start_date', $year);
        }

        if ($idBidwas) {
            $query->where('d_st.id_bidwas', $idBidwas);
        }

        $stats = $query->select('r_pegawai.nama', 'r_pegawai.nip', DB::raw('count(d_st.id_st) as total'))
            ->groupBy('r_pegawai.id', 'r_pegawai.nama', 'r_pegawai.nip')
            ->orderBy('total', 'desc')
            ->limit(50)
            ->get();

        $bidwas = DB::table('r_bidwas')->get()->map(function ($b) {
            $b->short_name = $b->nm_bidwas;
            if (str_contains($b->nm_bidwas, 'Instansi Pemerintah Pusat')) $b->short_name = 'IPP';
            elseif (str_contains($b->nm_bidwas, 'Pemerintah Daerah')) $b->short_name = 'APD';
            elseif (str_contains($b->nm_bidwas, 'Akuntan Negara')) $b->short_name = 'AN';
            elseif (str_contains($b->nm_bidwas, 'Investigasi')) $b->short_name = 'Investigasi';
            elseif (str_contains($b->nm_bidwas, 'Program dan Pelaporan')) $b->short_name = 'P3A';
            elseif (str_contains($b->nm_bidwas, 'Tata Usaha')) $b->short_name = 'TU';
            elseif (str_contains($b->nm_bidwas, 'Kepegawaian')) $b->short_name = 'KEP';
            elseif (str_contains($b->nm_bidwas, 'Keuangan')) $b->short_name = 'KEU';
            elseif (str_contains($b->nm_bidwas, 'Umum')) $b->short_name = 'UMM';
            return $b;
        });

        $years = DB::table('d_st')
            ->select(DB::raw('YEAR(start_date) as year'))
            ->whereNotNull('start_date')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $selectedBidwas = $idBidwas ? $bidwas->firstWhere('id_bidwas', $idBidwas) : null;

        return view('dashboard.monthly', compact('stats', 'month', 'year', 'startDate', 'endDate', 'bidwas', 'idBidwas', 'selectedBidwas', 'years', 'search'));
    }

    public function employeeDetail(Request $request, $nip)
    {
        $employee = DB::table('r_pegawai')->where('nip', $nip)->first();
        if (!$employee) abort(404);

        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $query = DB::table('d_st')
            ->join('d_st_tim', 'd_st.id_st', '=', 'd_st_tim.id_st')
            ->where('d_st_tim.nip', $nip)
            ->select('d_st.*', 'd_st_tim.peran');

        if ($startDate && $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('d_st.start_date', [$startDate, $endDate])
                    ->orWhereBetween('d_st.end_date', [$startDate, $endDate])
                    ->orWhere(function ($sq) use ($startDate, $endDate) {
                        $sq->where('d_st.start_date', '<=', $startDate)
                            ->where('d_st.end_date', '>=', $endDate);
                    });
            });
        } else {
            $query->whereMonth('d_st.start_date', '<=', $month)
                ->whereMonth('d_st.end_date', '>=', $month)
                ->whereYear('d_st.start_date', $year);
        }

        $assignments = $query->orderBy('d_st.start_date', 'desc')->get();

        return view('dashboard.employee_detail', compact('employee', 'assignments', 'month', 'year', 'startDate', 'endDate'));
    }

    public function bidwas()
    {
        $bidwasData = DB::table('r_bidwas')
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

        $pegawaiCounts = DB::table('r_pegawai')
            ->select('id_bidwas', DB::raw('count(*) as total_pegawai'))
            ->whereNotNull('id_bidwas')
            ->groupBy('id_bidwas')
            ->pluck('total_pegawai', 'id_bidwas');

        foreach ($bidwasData as $item) {
            $item->total_pegawai = $pegawaiCounts[$item->id_bidwas] ?? 0;
        }

        return view('dashboard.bidwas', compact('bidwasData'));
    }

    public function bidwasDetail($id)
    {
        $bidwas = DB::table('r_bidwas')->where('id_bidwas', $id)->first();
        if (!$bidwas) abort(404);

        $today = Carbon::today()->toDateString();

        $pegawaiList = DB::table('r_pegawai')
            ->where('r_pegawai.id_bidwas', $id)
            ->leftJoin('d_st_tim', 'r_pegawai.nip', '=', 'd_st_tim.nip')
            ->leftJoin('d_st', function ($join) use ($today) {
                $join->on('d_st_tim.id_st', '=', 'd_st.id_st')
                    ->where('d_st.start_date', '<=', $today)
                    ->where('d_st.end_date', '>=', $today)
                    ->whereIn('d_st.status_st', ['Realisasi', 'Perpanjangan ST']);
            })
            ->select(
                'r_pegawai.nip',
                'r_pegawai.nama',
                'r_pegawai.user_role',
                DB::raw('count(distinct d_st.id_st) as active_tasks'),
                DB::raw('count(distinct d_st_tim.id_st) as total_assignments')
            )
            ->groupBy('r_pegawai.id', 'r_pegawai.nip', 'r_pegawai.nama', 'r_pegawai.user_role')
            ->orderByDesc('active_tasks')
            ->get();

        $totalST = DB::table('d_st')->where('id_bidwas', $id)->count();
        $totalLHP = DB::table('d_lhp')
            ->join('d_st', 'd_lhp.id_st', '=', 'd_st.id_st')
            ->where('d_st.id_bidwas', $id)
            ->count();
        $activeST = DB::table('d_st')
            ->where('id_bidwas', $id)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereIn('status_st', ['Realisasi', 'Perpanjangan ST'])
            ->count();

        return view('dashboard.bidwas_detail', compact('bidwas', 'pegawaiList', 'totalST', 'totalLHP', 'activeST'));
    }

    public function detail($id)
    {
        $st = DB::table('d_st')
            ->leftJoin('d_lhp', 'd_st.id_st', '=', 'd_lhp.id_st')
            ->where('d_st.id_st', $id)
            ->select('d_st.*', 'd_lhp.nomor_lhp', 'd_lhp.tanggal_lhp', 'd_lhp.status_lhp')
            ->first();

        if (!$st) {
            abort(404);
        }

        $tim = DB::table('d_st_tim')
            ->join('r_pegawai', 'd_st_tim.nip', '=', 'r_pegawai.nip')
            ->where('d_st_tim.id_st', $id)
            ->select('r_pegawai.nama', 'r_pegawai.nip', 'd_st_tim.peran')
            ->get();

        return view('dashboard.detail', compact('st', 'tim'));
    }

    public function stList(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $user = auth()->user();

        if ($user->role === 'pegawai') {
            $query = DB::table('d_st')
                ->join('d_st_tim', 'd_st.id_st', '=', 'd_st_tim.id_st')
                ->where('d_st_tim.nip', $user->nip)
                ->select('d_st.*');
        } else {
            $query = DB::table('d_st');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('d_st.nama_penugasan', 'like', "%{$search}%")
                    ->orWhere('d_st.no_surat_tugas', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('d_st.status_st', $status);
        }

        if ($startDate && $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('d_st.start_date', [$startDate, $endDate])
                    ->orWhereBetween('d_st.end_date', [$startDate, $endDate])
                    ->orWhere(function ($sq) use ($startDate, $endDate) {
                        $sq->where('d_st.start_date', '<=', $startDate)
                            ->where('d_st.end_date', '>=', $endDate);
                    });
            });
        }

        $stList = $query->orderBy('d_st.start_date', 'desc')->paginate(15);
        $statuses = DB::table('d_st')
            ->distinct()
            ->whereNotNull('status_st')
            ->pluck('status_st')
            ->map(fn($s) => trim($s))
            ->unique()
            ->values();

        return view('dashboard.st', compact('stList', 'search', 'status', 'statuses', 'startDate', 'endDate'));
    }

    public function lhpList(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $user = auth()->user();

        $query = DB::table('d_lhp')
            ->leftJoin('d_st', 'd_lhp.id_st', '=', 'd_st.id_st');

        if ($user->role === 'pegawai') {
            $query->whereExists(function ($q) use ($user) {
                $q->select(DB::raw(1))
                    ->from('d_st_tim')
                    ->whereColumn('d_st_tim.id_st', 'd_st.id_st')
                    ->where('d_st_tim.nip', $user->nip);
            });
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('d_lhp.nomor_lhp', 'like', "%{$search}%")
                    ->orWhere('d_st.nama_penugasan', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('d_lhp.status_lhp', $status);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('d_lhp.tanggal_lhp', [$startDate, $endDate]);
        }

        $lhpList = $query->select('d_lhp.*', 'd_st.nama_penugasan', 'd_st.id_st as st_id', 'd_st.no_surat_tugas')
            ->orderBy('d_lhp.tanggal_lhp', 'desc')
            ->paginate(15);

        $statuses = DB::table('d_lhp')
            ->distinct()
            ->whereNotNull('status_lhp')
            ->pluck('status_lhp')
            ->map(fn($s) => trim($s))
            ->unique()
            ->values();

        return view('dashboard.lhp', compact('lhpList', 'search', 'status', 'statuses', 'startDate', 'endDate'));
    }
}
