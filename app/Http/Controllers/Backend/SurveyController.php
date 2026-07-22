<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    // Aspek penilaian survei (kolom => label)
    private const ASPEK = [
        'kemudahan_akses'       => 'Kemudahan Akses',
        'kelengkapan_informasi' => 'Kelengkapan Informasi',
        'kecepatan_loading'     => 'Kecepatan Loading',
        'tampilan_antarmuka'    => 'Tampilan Antarmuka',
        'relevansi_pencarian'   => 'Relevansi Pencarian',
    ];

    public function index(Request $request)
    {
        $query = Survey::query();

        if ($request->filled('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }
        if ($request->filled('jenis_pengguna')) {
            $query->where('jenis_pengguna', $request->jenis_pengguna);
        }

        $data = $query->latest()->paginate(15)->withQueryString();

        // Statistik (dari seluruh data, bukan hasil filter)
        $rataAspek = [];
        foreach (self::ASPEK as $kolom => $label) {
            $rataAspek[$label] = round(Survey::avg($kolom) ?? 0, 1);
        }
        $stats = [
            'total'      => Survey::count(),
            'rata_aspek' => $rataAspek,
            'rata_total' => $rataAspek ? round(array_sum($rataAspek) / count($rataAspek), 1) : 0,
        ];

        return view('backend.survey', [
            'data'       => $data,
            'stats'      => $stats,
            'aspek'      => self::ASPEK,
            'hasFilter'  => $request->hasAny(['nama', 'jenis_pengguna']),
        ]);
    }

    public function show($id)
    {
        $item = Survey::findOrFail($id);

        return view('backend.survey-view', [
            'item'  => $item,
            'aspek' => self::ASPEK,
        ]);
    }

    public function destroy($id)
    {
        Survey::findOrFail($id)->delete();

        return redirect()->route('backend.survei.index')
            ->with('success', 'Jawaban survei berhasil dihapus.');
    }
}
