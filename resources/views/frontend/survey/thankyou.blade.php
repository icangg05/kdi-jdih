<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih - Survei JDIH Kota Kendari</title>
    
    <!-- Tailwind CSS CDN (jika tidak menggunakan Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Atau jika menggunakan Vite, pastikan file CSS ada -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .success-animation {
            animation: success-bounce 1s ease;
        }
        
        @keyframes success-bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-10px);}
            60% {transform: translateY(-5px);}
        }
        
        .stat-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.4); }
            70% { box-shadow: 0 0 0 20px rgba(102, 126, 234, 0); }
            100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-purple-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-lg w-full">
            <!-- Success Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8 success-animation">
                <div class="gradient-bg p-8 text-center text-white">
                    <div class="mx-auto w-28 h-28 bg-white/20 rounded-full flex items-center justify-center mb-6 pulse-animation">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    
                    <h1 class="text-4xl font-bold mb-4">Terima Kasih! 🙏</h1>
                    <p class="text-lg opacity-90 mb-2">
                        Survei kepuasan Anda telah berhasil dikirim.
                    </p>
                    <p class="text-md opacity-80">
                        Kontribusi Anda membantu kami meningkatkan layanan JDIH Kota Kendari.
                    </p>
                </div>
                
                <!-- Success Message -->
                @if(session('success'))
                <div class="p-6 bg-green-50 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Stats Section -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                    <span class="inline-block mr-2">📊</span> 
                    Kontribusi Anda
                </h2>
                
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div class="stat-card bg-blue-50 p-6 rounded-xl border border-blue-100 text-center">
                        <div class="text-4xl font-bold text-blue-600 mb-2">{{ $total_survei ?? '0' }}</div>
                        <p class="text-gray-600 font-medium">Total Survei</p>
                        <div class="mt-2 text-sm text-blue-500">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                            </svg>
                            +1 dari Anda
                        </div>
                    </div>
                    
                    <div class="stat-card bg-green-50 p-6 rounded-xl border border-green-100 text-center">
                        <div class="text-4xl font-bold text-green-600 mb-2">{{ $average_rating ?? '0' }}/5</div>
                        <p class="text-gray-600 font-medium">Rating Rata-rata</p>
                        <div class="mt-2">
                            <div class="flex justify-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($average_rating ?? 0))
                                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Detail Ratings -->
                <div class="space-y-4">
                    <h3 class="font-semibold text-gray-700 mb-3">📈 Detail Penilaian</h3>
                    
                    @foreach([
                        ['label' => 'Kemudahan Akses', 'value' => $average_kemudahan ?? 0, 'color' => 'blue'],
                        ['label' => 'Kelengkapan Info', 'value' => $average_kelengkapan ?? 0, 'color' => 'green'],
                        ['label' => 'Kecepatan Loading', 'value' => $average_kecepatan ?? 0, 'color' => 'purple'],
                        ['label' => 'Tampilan Website', 'value' => $average_tampilan ?? 0, 'color' => 'pink'],
                        ['label' => 'Relevansi Hasil', 'value' => $average_relevansi ?? 0, 'color' => 'orange'],
                    ] as $item)
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="font-medium text-gray-600">{{ $item['label'] }}</span>
                            <span class="font-bold text-gray-800">{{ $item['value'] }}/5</span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-{{ $item['color'] }}-500 rounded-full" 
                                 style="width: {{ ($item['value'] / 5) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <a href="{{ url('/') }}" 
                   class="block w-full gradient-bg text-white py-4 px-6 rounded-xl font-semibold text-lg hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1 text-center">
                    <span class="inline-block mr-2">🏠</span> 
                    Kembali ke Beranda JDIH
                </a>
                
                <a href="{{ route('survey.create') }}" 
                   class="block w-full bg-white text-gray-800 border-2 border-gray-300 py-4 px-6 rounded-xl font-semibold text-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 text-center">
                    <span class="inline-block mr-2">📝</span> 
                    Isi Survei Lagi
                </a>
            </div>

            <!-- Footer -->
            <div class="mt-10 text-center">
                <div class="text-gray-500 text-sm mb-2">
                    JDIH Kota Kendari - Sistem Dokumentasi dan Informasi Hukum
                </div>
                <div class="text-gray-400 text-xs">
                    © {{ date('Y') }} Dinas Komunikasi dan Informatika Kota Kendari
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript for animations -->
    <script>
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stat cards on hover
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-5px)';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0)';
                });
            });
            
            // Show success message with delay
            setTimeout(() => {
                const successMsg = document.querySelector('.success-animation');
                if (successMsg) {
                    successMsg.style.opacity = '1';
                }
            }, 100);
        });
    </script>
</body>
</html>