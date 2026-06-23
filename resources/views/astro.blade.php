<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AstroGuard - NASA Asteroid Tracker</title>
    <!-- Tailwind CSS untuk styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 p-8 min-h-screen font-sans">

    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-10 text-center">
            <h1 class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 mb-3">
                🚀 AstroGuard Radar
            </h1>
            <p class="text-gray-400 text-lg">Sistem Pemantauan Asteroid Dekat Bumi (Data Real-time dari NASA API)</p>
        </div>

        <!-- Form Pencarian Berdasarkan Tanggal -->
        <div class="bg-gray-800 p-6 rounded-xl shadow-lg mb-10 max-w-2xl mx-auto border border-gray-700">
            <form method="GET" action="/" class="flex flex-col md:flex-row gap-4 items-center">
                <div class="w-full">
                    <label class="block text-sm text-gray-400 mb-2">Pilih Tanggal Pantauan:</label>
                    <input type="date" name="date" value="{{ $searchDate }}" required
                           class="w-full p-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="w-full md:w-auto mt-auto">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition duration-200 shadow-lg shadow-blue-600/30">
                        Scan Ruang Angkasa
                    </button>
                </div>
            </form>
        </div>

        <!-- Indikator Total -->
        <div class="mb-6 flex justify-between items-end border-b border-gray-700 pb-2">
            <h2 class="text-2xl font-bold text-gray-200">Hasil Pemindaian: {{ date('d F Y', strtotime($searchDate)) }}</h2>
            <span class="bg-blue-900 text-blue-200 py-1 px-3 rounded-full text-sm font-semibold">
                Ditemukan {{ $totalCount }} Asteroid
            </span>
        </div>

        <!-- Error Handling -->
        @if($isError)
            <div class="bg-red-900/50 border border-red-500 text-red-200 p-4 rounded-lg mb-6">
                ⚠️ Gagal terhubung ke server NASA. Silakan coba lagi nanti atau periksa koneksi internet Anda.
            </div>
        @endif

        <!-- Menampilkan Hasil API -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            @forelse ($asteroids as $ast)
                <!-- Kartu Asteroid -->
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden hover:border-blue-500 transition duration-300 shadow-lg group">
                    
                    <!-- Header Kartu -->
                    <div class="p-5 border-b border-gray-700 bg-gray-800/50 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white group-hover:text-blue-400 transition">{{ $ast['name'] }}</h3>
                        
                        <!-- Indikator Bahaya (Hazardous) -->
                        @if($ast['is_potentially_hazardous_asteroid'])
                            <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 rounded animate-pulse">BERBAHAYA</span>
                        @else
                            <span class="bg-green-600 text-white text-xs font-bold px-2 py-1 rounded">AMAN</span>
                        @endif
                    </div>

                    <!-- Body Kartu: Data Teknis dari NASA -->
                    <div class="p-5 space-y-4">
                        <!-- Diameter -->
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Estimasi Ukuran (Diameter)</p>
                            <p class="text-lg text-gray-200 font-mono">
                                {{ number_format($ast['estimated_diameter']['meters']['estimated_diameter_min'], 2) }}m - 
                                {{ number_format($ast['estimated_diameter']['meters']['estimated_diameter_max'], 2) }}m
                            </p>
                        </div>

                        <!-- Kecepatan Relatif -->
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Kecepatan Melintas</p>
                            <p class="text-lg text-gray-200 font-mono">
                                {{ number_format($ast['close_approach_data'][0]['relative_velocity']['kilometers_per_hour'], 2) }} km/jam
                            </p>
                        </div>

                        <!-- Jarak Meleset (Miss Distance) -->
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Jarak dari Bumi</p>
                            <p class="text-lg text-blue-300 font-mono font-bold">
                                {{ number_format($ast['close_approach_data'][0]['miss_distance']['kilometers'], 2) }} km
                            </p>
                        </div>
                    </div>
                    
                    <!-- Footer Kartu -->
                    <div class="bg-gray-900 p-3 text-center text-xs text-gray-500">
                        NASA JPL ID: {{ $ast['id'] }}
                    </div>
                </div>
            @empty
                @if(!$isError)
                    <div class="col-span-full p-8 text-center bg-gray-800 border border-gray-700 text-gray-400 rounded-xl">
                        Tidak ada asteroid yang terdeteksi melintas dekat bumi pada tanggal ini.
                    </div>
                @endif
            @endforelse

        </div>
    </div>

</body>
</html>