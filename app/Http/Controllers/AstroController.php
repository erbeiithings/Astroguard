<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AstroController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input tanggal dari user (Kalo kosong, pake tanggal hari ini)
        $date = $request->input('date', date('Y-m-d'));

        // 2. Siapin data untuk nembak API NASA (NeoWs - Near Earth Object Web Service)
        $queryData = [
            'api_key' => env('NASA_API_KEY', 'DEMO_KEY'),
            'start_date' => $date,
            'end_date' => $date // Kita cari asteroid di tanggal yang sama
        ];

        // 3. Tembak API NASA
        $response = Http::get(env('NASA_API_URL') . 'feed', $queryData);

        $asteroids = [];
        $totalCount = 0;

        // 4. Kalo API sukses, ambil datanya
        if ($response->successful()) {
            $data = $response->json();
            $totalCount = $data['element_count']; // Total asteroid hari itu
            
            // NASA nyimpen data asteroidnya di dalam array berdasarkan tanggal
            if (isset($data['near_earth_objects'][$date])) {
                $asteroids = $data['near_earth_objects'][$date];
            }
        }

        // 5. Kirim data ke halaman web (View)
        return view('astro', [
            'asteroids' => $asteroids,
            'searchDate' => $date,
            'totalCount' => $totalCount,
            'isError' => $response->failed() // Cek kalo API lagi down
        ]);
    }
}