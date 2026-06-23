<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AstroGuard - NASA Web Service</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Styling buat tombol toggle/switch */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #ef4444;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #ef4444;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-200 min-h-screen">

    @if(isset($apod['url']))
    <div class="relative w-full h-80 bg-cover bg-center" style="background-image: url('{{ $apod['url'] }}');">
        <div class="absolute inset-0 bg-black bg-opacity-60 flex flex-col justify-center items-center text-center p-6">
            <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-2 tracking-wider">🚀 AstroGuard Tracker</h1>
            <p class="text-lg text-gray-300 max-w-2xl italic">"{{ $apod['title'] ?? 'Exploring the Cosmos' }}"</p>
        </div>
    </div>
    @else
    <div class="w-full bg-gray-800 p-10 text-center">
        <h1 class="text-4xl font-extrabold text-white mb-2 tracking-wider">🚀 AstroGuard Tracker</h1>
    </div>
    @endif

    <div class="max-w-7xl mx-auto p-6 md:p-10">
        
        <div class="bg-gray-800 p-6 rounded-xl shadow-lg border border-gray-700 mb-10 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-blue-400">Pilih Tanggal Observasi</h2>
                <p class="text-sm text-gray-400">Sinkronisasi data langsung dari server NASA</p>
            </div>
            <form method="GET" action="/" class="flex gap-3 w-full md:w-auto">
                <input type="date" name="date" value="{{ $date }}" class="bg-gray-700 text-white border border-gray-600 rounded-lg p-3 w-full focus:outline-none focus:border-blue-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                    Scan API
                </button>
            </form>
        </div>

        <div class="mb-12">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    ☄️ Radar Asteroid (<span id="asteroid-count">{{ count($asteroids) }}</span> Objek)
                </h2>
                
                <div class="flex items-center mt-4 md:mt-0 gap-3">
                    <span class="text-sm text-gray-400">Hanya tampilkan yang berbahaya</span>
                    <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                        <input type="checkbox" id="hazardToggle" onchange="filterHazards()" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer transition-transform duration-200 ease-in-out z-10"/>
                        <label for="hazardToggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-600 cursor-pointer transition-colors duration-200 ease-in-out"></label>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="asteroid-container">
                @forelse ($asteroids as $asteroid)
                    @php 
                        $isHazard = $asteroid['is_potentially_hazardous_asteroid'];
                        $speed = number_format($asteroid['close_approach_data'][0]['relative_velocity']['kilometers_per_hour'] ?? 0, 0, ',', '.');
                        $distance = number_format($asteroid['close_approach_data'][0]['miss_distance']['kilometers'] ?? 0, 0, ',', '.');
                    @endphp
                    
                    <div class="asteroid-card bg-gray-800 rounded-xl p-5 border {{ $isHazard ? 'border-red-500 shadow-[0_0_15px_rgba(239,68,68,0.3)]' : 'border-gray-700' }}" data-hazard="{{ $isHazard ? 'true' : 'false' }}">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="font-bold text-lg text-white truncate w-3/4">{{ $asteroid['name'] }}</h3>
                            @if($isHazard)
                                <span class="bg-red-900 text-red-300 text-xs font-bold px-2 py-1 rounded animate-pulse">BAHAYA</span>
                            @else
                                <span class="bg-green-900 text-green-300 text-xs px-2 py-1 rounded">Aman</span>
                            @endif
                        </div>
                        
                        <div class="space-y-2 text-sm text-gray-400">
                            <p class="flex justify-between"><span>📏 Diameter:</span> <span class="text-gray-200">{{ number_format($asteroid['estimated_diameter']['meters']['estimated_diameter_max'], 2) }} m</span></p>
                            <p class="flex justify-between"><span>🚀 Kecepatan:</span> <span class="text-gray-200">{{ $speed }} km/j</span></p>
                            <p class="flex justify-between"><span>🎯 Jarak Melintas:</span> <span class="text-gray-200">{{ $distance }} km</span></p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-gray-800 text-center p-8 rounded-xl border border-gray-700">
                        <p class="text-gray-400">Tidak ada data asteroid pada tanggal ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">🌍 Pantauan Rotasi Bumi (Satelit DSCOVR)</h2>
            @if(isset($earthPhotos) && count($earthPhotos) > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @php
                        // Memecah format YYYY-MM-DD biar bisa dipakai menyusun URL gambar NASA
                        $year = substr($date, 0, 4);
                        $month = substr($date, 5, 2);
                        $day = substr($date, 8, 2);
                    @endphp

                    @foreach($earthPhotos as $photo)
                        <div class="relative group overflow-hidden rounded-full bg-black border-4 border-gray-800 shadow-xl">
                            <img src="https://epic.gsfc.nasa.gov/archive/natural/{{$year}}/{{$month}}/{{$day}}/jpg/{{$photo['image']}}.jpg" 
                                 alt="Rotasi Bumi" 
                                 class="w-full h-auto object-cover transform transition duration-500 group-hover:scale-105">
                            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-70 p-3 translate-y-full group-hover:translate-y-0 transition duration-300 text-center">
                                <p class="text-xs text-white font-bold">Waktu Jepret: <br> {{ \Carbon\Carbon::parse($photo['date'])->format('H:i:s') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-800 text-center p-8 rounded-xl border border-gray-700">
                    <p class="text-gray-400">Belum ada tangkapan citra satelit pada tanggal ini (Satelit EPIC biasanya memiliki delay rilis data 1-2 hari).</p>
                </div>
            @endif
        </div>

    <footer class="mt-16 pt-8 pb-4 border-t border-gray-800 text-center">
            <p class="text-gray-500 text-sm md:text-base">
                🚀 <strong>AstroGuard Tracker</strong> adalah website <i>prototype</i>.<br>
                Dibangun secara khusus untuk memenuhi tugas dan evaluasi nilai mata kuliah Teknologi Web Service. -Rabbani
            </p>
        </footer>

    </div>

    <script>
        function filterHazards() {
            let isChecked = document.getElementById('hazardToggle').checked;
            let cards = document.querySelectorAll('.asteroid-card');
            let countSpan = document.getElementById('asteroid-count');
            let visibleCount = 0;

            cards.forEach(card => {
                let isHazard = card.getAttribute('data-hazard') === 'true';
                if (isChecked) {
                    if (isHazard) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                } else {
                    card.style.display = 'block';
                    visibleCount++;
                }
            });
            countSpan.innerText = visibleCount;
        }

</body>
</html>