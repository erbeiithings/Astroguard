<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AstroController extends Controller
{
    public function index(Request $request)
    {
        // Pake API Key dari .env (atau DEMO_KEY)
        $apiKey = env('NASA_API_KEY', 'DEMO_KEY');
        
        // Ambil tanggal dari input user, default-nya hari ini
        $date = $request->input('date', date('Y-m-d'));

        // 1. Tembak API APOD (Astronomy Picture of the Day)
        $apodResponse = Http::get("https://api.nasa.gov/planetary/apod", [
            'api_key' => $apiKey,
            'date' => $date
        ]);
        $apod = $apodResponse->successful() ? $apodResponse->json() : null;

        // 2. Tembak API Asteroid (NEO)
        $neoResponse = Http::get("https://api.nasa.gov/neo/rest/v1/feed", [
            'start_date' => $date,
            'end_date' => $date,
            'api_key' => $apiKey
        ]);
        $asteroids = [];
        if ($neoResponse->successful()) {
            $neoData = $neoResponse->json();
            $asteroids = $neoData['near_earth_objects'][$date] ?? [];
        }

       // 3. Tembak API EPIC (Satelit DSCOVR Pemantau Rotasi Bumi)
        $epicResponse = Http::get("https://api.nasa.gov/EPIC/api/natural/date/{$date}", [
            'api_key' => $apiKey
        ]);
        $earthPhotos = [];
        if ($epicResponse->successful()) {
            // Ambil maksimal 4 frame rotasi bumi biar tampilannya pas sebaris
            $earthPhotos = array_slice($epicResponse->json() ?? [], 0, 4);
        }

        // Lempar ke tampilan web
        return view('astro', [
            'date' => $date,
            'apod' => $apod,
            'asteroids' => $asteroids,
            'marsPhotos' => $marsPhotos
        ]);
    }
}