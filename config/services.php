<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | AI Search Services Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk layanan AI yang digunakan dalam pencarian dokumen hukum
    |
    */

    'ai_search' => [
        // Provider utama untuk AI Search (google_gemini, openai, azure, fallback)
        'provider' => env('AI_SEARCH_PROVIDER', 'fallback'),
        
        // Enable/disable fitur AI Search
        'enabled' => env('AI_SEARCH_ENABLED', true),
        
        // Konfigurasi hasil pencarian
        'max_results' => env('AI_SEARCH_MAX_RESULTS', 10),
        'min_accuracy' => env('AI_SEARCH_MIN_ACCURACY', 30),
        'timeout' => env('AI_SEARCH_TIMEOUT', 15),
        
        // Cache untuk meningkatkan performa
        'cache' => [
            'enabled' => env('AI_SEARCH_CACHE_ENABLED', true),
            'ttl' => env('AI_SEARCH_CACHE_TTL', 300), // 5 menit
            'prefix' => 'ai_search_',
        ],
        
        // Fallback jika API error
        'fallback' => [
            'enabled' => env('AI_SEARCH_FALLBACK_ENABLED', true),
            'message' => 'Sistem sedang menggunakan penjelasan otomatis berdasarkan data dokumen.',
        ],
        
        // Logging untuk debugging
        'logging' => [
            'queries' => env('LOG_SEARCH_QUERIES', false),
            'responses' => env('LOG_AI_RESPONSES', false),
            'errors' => env('LOG_AI_ERRORS', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Gemini AI
    |--------------------------------------------------------------------------
    |
    | Google's Gemini AI (formerly Bard) - Free up to 60 requests per minute
    | Get API key: https://makersuite.google.com/app/apikey
    |
    */
    'google_gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-pro'), // gemini-pro, gemini-pro-vision
        'max_tokens' => env('GEMINI_MAX_TOKENS', 150),
        'temperature' => env('GEMINI_TEMPERATURE', 0.7),
        'top_p' => env('GEMINI_TOP_P', 0.9),
        'top_k' => env('GEMINI_TOP_K', 40),
        'timeout' => env('GEMINI_TIMEOUT', 10),
        'base_url' => 'https://generativelanguage.googleapis.com/v1beta/models/',
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI (ChatGPT)
    |--------------------------------------------------------------------------
    |
    | OpenAI GPT models - Paid service
    | Get API key: https://platform.openai.com/api-keys
    |
    */
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORG_ID'),
        'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'), // gpt-3.5-turbo, gpt-4, gpt-4-turbo-preview
        'max_tokens' => env('OPENAI_MAX_TOKENS', 150),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
        'timeout' => env('OPENAI_TIMEOUT', 10),
        'base_url' => 'https://api.openai.com/v1/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Azure OpenAI
    |--------------------------------------------------------------------------
    |
    | Microsoft Azure OpenAI Service
    |
    */
    'azure_openai' => [
        'api_key' => env('AZURE_OPENAI_KEY'),
        'endpoint' => env('AZURE_OPENAI_ENDPOINT'),
        'deployment' => env('AZURE_OPENAI_DEPLOYMENT'),
        'api_version' => env('AZURE_OPENAI_API_VERSION', '2023-05-15'),
        'timeout' => env('AZURE_OPENAI_TIMEOUT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Engine Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk search engine internal
    |
    */
    'search' => [
        'driver' => env('SEARCH_DRIVER', 'database'), // database, elasticsearch, meilisearch, algolia
        'max_results' => env('SEARCH_MAX_RESULTS', 50),
        'fuzzy' => env('SEARCH_FUZZY', true),
        'weights' => [
            'title' => 0.6,
            'description' => 0.3,
            'content' => 0.1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Services
    |--------------------------------------------------------------------------
    */

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Socialite Services (OAuth)
    |--------------------------------------------------------------------------
    */

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Storage Services
    |--------------------------------------------------------------------------
    */

    's3' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION'),
        'bucket' => env('AWS_BUCKET'),
        'url' => env('AWS_URL'),
        'endpoint' => env('AWS_ENDPOINT'),
        'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
    ],

    'cloudinary' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
        'secure' => env('CLOUDINARY_SECURE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Services
    |--------------------------------------------------------------------------
    */

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'from' => env('TWILIO_FROM'),
    ],

    'nexmo' => [
        'key' => env('NEXMO_KEY'),
        'secret' => env('NEXMO_SECRET'),
        'sms_from' => env('NEXMO_SMS_FROM'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Services
    |--------------------------------------------------------------------------
    */

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
        'is_3ds' => env('MIDTRANS_IS_3DS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Analytics Services
    |--------------------------------------------------------------------------
    */

    'google_analytics' => [
        'measurement_id' => env('GOOGLE_ANALYTICS_MEASUREMENT_ID'),
        'api_secret' => env('GOOGLE_ANALYTICS_API_SECRET'),
    ],

    'plausible' => [
        'domain' => env('PLAUSIBLE_DOMAIN'),
        'api_key' => env('PLAUSIBLE_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Map Services
    |--------------------------------------------------------------------------
    */

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'mapbox' => [
        'access_token' => env('MAPBOX_ACCESS_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Document Processing Services
    |--------------------------------------------------------------------------
    */

    'ocr' => [
        'driver' => env('OCR_DRIVER', 'tesseract'), // tesseract, google_vision, azure
        'tesseract' => [
            'path' => env('TESSERACT_PATH', '/usr/bin/tesseract'),
            'language' => env('TESSERACT_LANGUAGE', 'ind'),
        ],
        'google_vision' => [
            'key' => env('GOOGLE_VISION_API_KEY'),
        ],
    ],

    'pdf' => [
        'driver' => env('PDF_DRIVER', 'dompdf'), // dompdf, wkhtmltopdf, mpdf
        'wkhtmltopdf' => [
            'path' => env('WKHTMLTOPDF_PATH', '/usr/bin/wkhtmltopdf'),
        ],
    ],

];