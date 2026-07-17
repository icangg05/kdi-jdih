<?php
// config/ai-search.php

return [
    /*
    |--------------------------------------------------------------------------
    | AI Search System Configuration
    |--------------------------------------------------------------------------
    */

    'default' => [
        'enabled' => env('AI_SEARCH_ENABLED', true),
        
        // Provider options: 'google_gemini', 'openai', 'azure', 'fallback'
        'provider' => env('AI_SEARCH_PROVIDER', 'fallback'),
        
        // Search configuration
        'max_results' => env('AI_SEARCH_MAX_RESULTS', 10),
        'min_accuracy' => env('AI_SEARCH_MIN_ACCURACY', 30),
        'timeout' => env('AI_SEARCH_TIMEOUT', 15),
        
        // Cache configuration
        'cache' => [
            'enabled' => true,
            'ttl' => env('AI_SEARCH_CACHE_TTL', 300), // 5 minutes
            'prefix' => 'ai_search_',
        ],
        
        // Fallback configuration
        'fallback' => [
            'enabled' => env('AI_SEARCH_FALLBACK_ENABLED', true),
            'provider' => 'fallback',
        ],
        
        // Logging
        'logging' => [
            'queries' => env('LOG_SEARCH_QUERIES', false),
            'responses' => env('LOG_AI_RESPONSES', false),
            'performance' => env('LOG_SEARCH_PERFORMANCE', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Providers Configuration
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'google_gemini' => [
            'driver' => 'google_gemini',
            'api_key' => env('GEMINI_API_KEY'),
            'model' => 'gemini-pro',
            'max_tokens' => 150,
            'temperature' => 0.7,
        ],
        
        'openai' => [
            'driver' => 'openai',
            'api_key' => env('OPENAI_API_KEY'),
            'model' => 'gpt-3.5-turbo',
            'max_tokens' => 150,
            'temperature' => 0.7,
        ],
        
        'azure' => [
            'driver' => 'azure',
            'api_key' => env('AZURE_OPENAI_KEY'),
            'endpoint' => env('AZURE_OPENAI_ENDPOINT'),
            'deployment' => env('AZURE_OPENAI_DEPLOYMENT'),
            'api_version' => '2023-05-15',
        ],
        
        'fallback' => [
            'driver' => 'fallback',
            'name' => 'Fallback Explanation',
            'description' => 'Penjelasan otomatis berdasarkan data dokumen',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Document Search Configuration
    |--------------------------------------------------------------------------
    */
    'documents' => [
        // Kolom yang akan dicari
        'searchable_columns' => ['judul', 'deskripsi', 'ringkasan', 'konten'],
        
        // Bobot relevansi (total harus 1.0)
        'relevance_weights' => [
            'judul' => 0.6,
            'deskripsi' => 0.3,
            'ringkasan' => 0.1,
            'konten' => 0.0, // Biasanya tidak dicari karena terlalu panjang
        ],
        
        // Minimum similarity untuk ditampilkan (0-100)
        'min_similarity' => 30,
        
        // Maximum results
        'max_results' => 10,
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Explanation Templates (untuk fallback)
    |--------------------------------------------------------------------------
    */
    'explanations' => [
        'templates' => [
            'found_documents' => "Berdasarkan pencarian untuk ':query', ditemukan :count dokumen hukum terkait. Dokumen-dokumen ini termasuk dalam kategori: :categories. Hasil pencarian diurutkan berdasarkan relevansi dengan pertanyaan Anda.",
            'no_documents' => "Pencarian untuk ':query' tidak menemukan dokumen yang relevan. Disarankan untuk menggunakan kata kunci yang lebih spesifik atau konsultasi dengan admin.",
            'general' => "Pencarian Anda tentang ':query' terkait dengan dokumen hukum di Kota Kendari. Sistem menemukan :count dokumen yang mungkin relevan dengan kebutuhan Anda.",
        ],
        
        'categories' => [
            'peraturan' => 'Peraturan Daerah',
            'monografi' => 'Buku dan Publikasi',
            'artikel' => 'Artikel Hukum',
            'putusan' => 'Putusan Pengadilan',
            'pengumuman' => 'Pengumuman Resmi',
            'informasi' => 'Informasi Hukum',
        ],
    ],
];