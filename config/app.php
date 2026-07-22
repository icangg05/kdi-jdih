<?php

return [
  /*
    |--------------------------------------------------------------------------
    | My Custom Data
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

  'doc_directory' => 'dokumen/',
  'img_directory' => 'gambar/',
  'default_img'   => 'assets/img/default-img.jpg',

  // Contact & social media
  'contact' => '0821-3527-3000',
  'email'   => 'pemkot@kendarikota.go.id',
  'address' => 'Jl. Drs. H. Abd. Silondae No. 8 , Kel. Mandonga Kec. Mandonga 93111',
  // Social media
  'fb'      => [
    'url'   => 'https://www.facebook.com/share/1armp948ym/',
    'label' => 'JDIH Kota Kendari',
  ],
  'ig' => [
    'url'   => 'https://www.instagram.com/jdihkotakendari2?igsh=MWVqNDN6Y3Y4ZmtjcA==',
    'label' => 'jdihkotakendari2',
  ],
  'yt' => [
    'url'   => 'https://www.youtube.com/@jdihkotakendari',
    'label' => 'JDIH KENDARI',
  ],
  'tt' => [
    'url'   => 'https://www.tiktok.com/@jdihkotakendari',
    'label' => 'JDIH',
  ],
  'surveiUrl' => 'https://forms.gle/wtEHAoVe7EcZ8AHx6',

  'menus' => [
    [
        'label' => 'Beranda',
        'route' => 'frontend.beranda',
    ],
    [
        'label'       => 'Profil',
        'startActive' => 'profil',
        'sub'         => [
            ['label' => 'Sekilas Sejarah', 'route' => 'frontend.profil', 'param' => 'sekilas-sejarah'],
            ['label' => 'Dasar Hukum', 'route' => 'frontend.profil', 'param' => 'dasar-hukum'],
            ['label' => 'Visi', 'route' => 'frontend.profil', 'param' => 'visi'],
            ['label' => 'Misi', 'route' => 'frontend.profil', 'param' => 'misi'],
            ['label' => 'Struktur Organisasi', 'route' => 'frontend.profil', 'param' => 'sto'],
        ],
    ],
    [
        'label'       => 'Jenis Dokumen',
        'startActive' => 'dokumen',
        'sub'         => [
            ['label' => 'Peraturan & Keputusan', 'route' => 'frontend.dokumen.index', 'param' => 'peraturan'],
            ['label' => 'Monografi', 'route' => 'frontend.dokumen.index', 'param' => 'monografi'],
            ['label' => 'Artikel / Majalah Hukum', 'route' => 'frontend.dokumen.index', 'param' => 'artikel'],
            ['label' => 'Putusan', 'route' => 'frontend.dokumen.index', 'param' => 'putusan'],
        ],
    ],
    [
        'label'       => 'Informasi Hukum',
        // Pembentukan PUU tampil sebagai submenu di sini, tapi URL-nya beda prefix
        'startActive' => ['informasi-hukum', 'pembentukan-puu'],
        'sub'         => [
            // POSISI PALING ATAS: Menu Pembentukan PUU (tanpa submenu di sini)
            [
                'label' => 'Pembentukan PUU',
                'route' => 'frontend.pembentukan-puu.index',
                // TIDAK ADA SUB MENU DI SINI - Akan ada di halaman sendiri
            ],
            // Data dari database akan ditambahkan di bawah ini oleh header
        ],
    ],
    [
        'label' => 'LAYANAN DISABILITAS',
        'route' => 'frontend.disabilitas',
        // dua prefix: /disabilitas (route alternatif) & /layanan-disabilitas (index + detail)
        'startActive' => ['disabilitas', 'layanan-disabilitas'],
    ],
    [
        'label'       => 'Publikasi',
        'startActive' => ['berita', 'pengumuman', 'video'],
        'sub'         => [
            ['label' => 'Berita', 'route' => 'frontend.berita.index'],
            ['label' => 'Pengumuman', 'route' => 'frontend.pengumuman.index'],
            ['label' => 'Video', 'route' => 'frontend.video.index'],
        ],
    ]
],


  /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

  'name' => env('APP_NAME', 'Laravel'),

  /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

  'env' => env('APP_ENV', 'production'),

  /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

  'debug' => (bool) env('APP_DEBUG', false),

  /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

  'url' => env('APP_URL', 'http://localhost'),

  /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

  'timezone' => 'Asia/makassar',

  /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

  'locale' => env('APP_LOCALE', 'id'),

  // Bahasa yang didukung halaman publik (multi-bahasa)
  'supported_locales' => ['id', 'en', 'zh', 'ko'],

  'fallback_locale' => env('APP_FALLBACK_LOCALE', 'id'),

  'faker_locale' => env('APP_FAKER_LOCALE', 'id_ID'),

  /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

  'cipher' => 'AES-256-CBC',

  'key' => env('APP_KEY'),

  'previous_keys' => [
    ...array_filter(
      explode(',', env('APP_PREVIOUS_KEYS', ''))
    ),
  ],

  /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

  'maintenance' => [
    'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
    'store'  => env('APP_MAINTENANCE_STORE', 'database'),
  ],

];