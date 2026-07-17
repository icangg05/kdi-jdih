<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StatistikController extends Controller
{
    public function getStatistikData()
    {
        // Cache data statistik selama 1 jam untuk performa
        return Cache::remember('frontend_statistik_data', 3600, function() {
            $currentYear = date('Y');
            $startYear = $currentYear - 4;
            $years = range($startYear, $currentYear);

            // Hitung semua statistik
            $data = [
                'totalDokumenHukum' => DB::table('document')->count() + 
                                      DB::table('pembentukan_puu')->count() + 
                                      DB::table('disabilitas')->count(),
                
                'totalKoleksiPUU' => DB::table('document')->where('tipe_dokumen', 1)->count() + 
                                    DB::table('pembentukan_puu')->count(),
                
                'totalAkses' => DB::table('document')->sum('views') + 
                               DB::table('pembentukan_puu')->sum('views') + 
                               DB::table('disabilitas')->sum('views'),
                
                'totalDownload' => DB::table('document')->sum('jumlah_download') + 
                                  DB::table('pembentukan_puu')->sum('jumlah_download') + 
                                  DB::table('disabilitas')->sum('jumlah_download'),
                
                'statusKeberlakuan' => DB::table('document')
                    ->where('tipe_dokumen', 1)
                    ->select('status_keberlakuan', DB::raw('COUNT(*) as total'))
                    ->groupBy('status_keberlakuan')
                    ->get()
                    ->concat(DB::table('pembentukan_puu')
                        ->select('status_dokumen as status_keberlakuan', DB::raw('COUNT(*) as total'))
                        ->groupBy('status_dokumen')
                        ->get()),
                
                'jenisPUU' => DB::table('document')
                    ->where('tipe_dokumen', 1)
                    ->select('jenis_peraturan', DB::raw('COUNT(*) as total'))
                    ->groupBy('jenis_peraturan')
                    ->get()
                    ->concat(DB::table('pembentukan_puu')
                        ->select('jenis_dokumen as jenis_peraturan', DB::raw('COUNT(*) as total'))
                        ->groupBy('jenis_dokumen')
                        ->get()),
                
                'years' => $years,
            ];

            // Data timeline
            foreach ($years as $year) {
                $data['peraturanData'][] = DB::table('document')
                    ->where('tipe_dokumen', 1)
                    ->whereYear('created_at', $year)
                    ->count();
                
                $data['monografiData'][] = DB::table('document')
                    ->where('tipe_dokumen', 2)
                    ->whereYear('created_at', $year)
                    ->count();
                
                $data['putusanData'][] = DB::table('document')
                    ->where('tipe_dokumen', 4)
                    ->whereYear('created_at', $year)
                    ->count();
                
                $data['pembentukanData'][] = DB::table('pembentukan_puu')
                    ->whereYear('created_at', $year)
                    ->count();
                
                $data['disabilitasData'][] = DB::table('disabilitas')
                    ->whereYear('created_at', $year)
                    ->count();
            }

            return $data;
        });
    }
}