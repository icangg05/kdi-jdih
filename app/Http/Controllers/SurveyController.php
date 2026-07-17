<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    // Tampilkan form survei
    public function create()
    {
        return view('frontend.survey.form');
    }

    // Simpan survei
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'instansi' => 'nullable|string|max:255',
            'jenis_pengguna' => 'required|in:Mahasiswa,Akademisi,Praktisi Hukum,Masyarakat Umum,Lainnya',
            'kemudahan_akses' => 'required|integer|between:1,5',
            'kelengkapan_informasi' => 'required|integer|between:1,5',
            'kecepatan_loading' => 'required|integer|between:1,5',
            'tampilan_antarmuka' => 'required|integer|between:1,5',
            'relevansi_pencarian' => 'required|integer|between:1,5',
            'saran_perbaikan' => 'nullable|string|max:1000',
            'fitur_harapan' => 'nullable|string|max:1000',
            'bersedia_dihubungi' => 'boolean',
            'kontak' => 'nullable|string|max:255',
        ]);

        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();
        $validated['bersedia_dihubungi'] = $request->has('bersedia_dihubungi');

        Survey::create($validated);

        return redirect()->route('survey.thankyou')->with('success', 'Terima kasih atas partisipasi Anda dalam survei ini!');
    }

    // Halaman terima kasih dengan statistik
    public function thankyou()
    {
        // Ambil statistik survei
        $totalSurvei = Survey::count();
        
        // Hitung rating rata-rata
        $averageKemudahan = Survey::avg('kemudahan_akses') ?? 0;
        $averageKelengkapan = Survey::avg('kelengkapan_informasi') ?? 0;
        $averageKecepatan = Survey::avg('kecepatan_loading') ?? 0;
        $averageTampilan = Survey::avg('tampilan_antarmuka') ?? 0;
        $averageRelevansi = Survey::avg('relevansi_pencarian') ?? 0;
        
        $averageRating = ($averageKemudahan + $averageKelengkapan + $averageKecepatan + 
                         $averageTampilan + $averageRelevansi) / 5;
        
        // Format angka
        $averageRating = round($averageRating, 1);
        
        // Data untuk chart (opsional)
        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingDistribution[$i] = Survey::where('kemudahan_akses', $i)->count() +
                                     Survey::where('kelengkapan_informasi', $i)->count() +
                                     Survey::where('kecepatan_loading', $i)->count() +
                                     Survey::where('tampilan_antarmuka', $i)->count() +
                                     Survey::where('relevansi_pencarian', $i)->count();
            $ratingDistribution[$i] = round($ratingDistribution[$i] / 5); // rata-rata per rating
        }

        return view('frontend.survey.thankyou', [
            'total_survei' => $totalSurvei,
            'average_rating' => $averageRating,
            'rating_distribution' => $ratingDistribution,
            'average_kemudahan' => round($averageKemudahan, 1),
            'average_kelengkapan' => round($averageKelengkapan, 1),
            'average_kecepatan' => round($averageKecepatan, 1),
            'average_tampilan' => round($averageTampilan, 1),
            'average_relevansi' => round($averageRelevansi, 1),
        ]);
    }

    // Statistik survei untuk dashboard admin
    public function getStatistikSurvei()
    {
        $total = Survey::count();
        $averageKemudahan = Survey::avg('kemudahan_akses');
        $averageKelengkapan = Survey::avg('kelengkapan_informasi');
        $averageKecepatan = Survey::avg('kecepatan_loading');
        $averageTampilan = Survey::avg('tampilan_antarmuka');
        $averageRelevansi = Survey::avg('relevansi_pencarian');
        
        $jenisPengguna = Survey::select('jenis_pengguna', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_pengguna')
            ->get();

        $recentSurveys = Survey::latest()->take(5)->get();

        return [
            'total' => $total,
            'average_kemudahan' => round($averageKemudahan, 1),
            'average_kelengkapan' => round($averageKelengkapan, 1),
            'average_kecepatan' => round($averageKecepatan, 1),
            'average_tampilan' => round($averageTampilan, 1),
            'average_relevansi' => round($averageRelevansi, 1),
            'average_overall' => round(($averageKemudahan + $averageKelengkapan + $averageKecepatan + $averageTampilan + $averageRelevansi) / 5, 1),
            'jenis_pengguna' => $jenisPengguna,
            'recent_surveys' => $recentSurveys,
            'rating_distribution' => $this->getRatingDistribution(),
        ];
    }

    private function getRatingDistribution()
    {
        $ratings = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratings[$i] = [
                'kemudahan' => Survey::where('kemudahan_akses', $i)->count(),
                'kelengkapan' => Survey::where('kelengkapan_informasi', $i)->count(),
                'kecepatan' => Survey::where('kecepatan_loading', $i)->count(),
                'tampilan' => Survey::where('tampilan_antarmuka', $i)->count(),
                'relevansi' => Survey::where('relevansi_pencarian', $i)->count(),
            ];
        }
        return $ratings;
    }
}