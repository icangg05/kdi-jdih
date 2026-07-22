<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JdihKendariApiController;
use App\Http\Controllers\Api\MobileApiController;

/*
|--------------------------------------------------------------------------
| API Routes for JDIH Kota Kendari
|--------------------------------------------------------------------------
|
| API untuk integrasi dengan JDIHN.go.id dan akses publik
|
*/

// ==============================================
// PUBLIC TEST ENDPOINTS
// ==============================================

Route::get('/test', function () {
  return response()->json(['message' => 'JDIH Kota Kendari API is working']);
});

// ==============================================
// MAIN JDIH API ROUTES
// ==============================================

Route::prefix('jdih')->group(function () {
  // 1. Health Check
  Route::get('/health', [JdihKendariApiController::class, 'health']);
  Route::get('/health-check', [JdihKendariApiController::class, 'healthCheck']); // alias

  // 2. Main API Endpoints (membutuhkan API Key)
  Route::get('/search', [JdihKendariApiController::class, 'search']);
  Route::get('/documents/{id}', [JdihKendariApiController::class, 'show']);
  Route::get('/documents/{id}/download', [JdihKendariApiController::class, 'download']);
  Route::get('/statistics', [JdihKendariApiController::class, 'statistics']);
  Route::get('/document-types', [JdihKendariApiController::class, 'documentTypes']);
  Route::get('/all-documents', [JdihKendariApiController::class, 'getAllDocuments']);

  // 3. JDIHN FORMAT ENDPOINTS (untuk integrasi JDIHN.go.id)
  Route::prefix('jdihn-format')->group(function () {
    Route::get('/search', [JdihKendariApiController::class, 'jdihnSearch']);
    Route::get('/documents/{id}', [JdihKendariApiController::class, 'jdihnShow']);
    Route::get('/all', [JdihKendariApiController::class, 'jdihnAll']); // UTAMA untuk sinkronisasi
  });
});

// ==============================================
// MOBILE API (Flutter) — read-only, frontend publik
// Kontrak: mobile-flutter/02-API-CONTRACT.md
// ==============================================
Route::prefix('v1')
  ->middleware(['mobile.api', 'throttle:120,1'])
  ->group(function () {
    Route::get('/meta',                    [MobileApiController::class, 'meta']);
    Route::get('/home',                    [MobileApiController::class, 'home']);

    Route::get('/documents',               [MobileApiController::class, 'documents']);
    Route::get('/documents/{id}',          [MobileApiController::class, 'documentShow'])->whereNumber('id');
    Route::get('/documents/{id}/download', [MobileApiController::class, 'documentDownload'])->whereNumber('id');
    Route::get('/document-filters',        [MobileApiController::class, 'documentFilters']);

    Route::post('/ai-search',              [MobileApiController::class, 'aiSearch']);

    Route::get('/news',                    [MobileApiController::class, 'news']);
    Route::get('/news/{id}',               [MobileApiController::class, 'newsShow'])->whereNumber('id');

    Route::get('/announcements',           [MobileApiController::class, 'announcements']);
    Route::get('/announcements/{id}',      [MobileApiController::class, 'announcementShow'])->whereNumber('id');

    Route::get('/legal-info/types',        [MobileApiController::class, 'legalInfoTypes']);
    Route::get('/legal-info',              [MobileApiController::class, 'legalInfo']);
    Route::get('/legal-info/{id}',         [MobileApiController::class, 'legalInfoShow'])->whereNumber('id');

    Route::get('/videos',                  [MobileApiController::class, 'videos']);
    Route::get('/profile/{kategori}',      [MobileApiController::class, 'profile']);

    Route::get('/disability',              [MobileApiController::class, 'disability']);
    Route::get('/disability/{id}',         [MobileApiController::class, 'disabilityShow'])->whereNumber('id');

    Route::get('/puu',                     [MobileApiController::class, 'puu']);
    Route::get('/puu/{id}',                [MobileApiController::class, 'puuShow'])->whereNumber('id');

    Route::post('/survey',                 [MobileApiController::class, 'survey']);
  });

// ==============================================
// SIMPLE DIRECT ROUTES (untuk testing cepat)
// ==============================================

// Route untuk testing langsung tanpa prefix
Route::get('/api-status', function () {
  return response()->json([
    'status' => 'online',
    'service' => 'JDIH Kota Kendari API',
    'version' => '1.0.0',
    'timestamp' => now()->toDateTimeString(),
    'endpoints' => [
      [
        'url' => '/api/jdih/health',
        'method' => 'GET',
        'description' => 'Health check API'
      ],
      [
        'url' => '/api/jdih/jdihn-format/all',
        'method' => 'GET',
        'description' => 'Semua dokumen format JDIHN (untuk sinkronisasi)'
      ],
      [
        'url' => '/api/jdih/search',
        'method' => 'GET',
        'description' => 'Pencarian dokumen (butuh API Key)'
      ]
    ]
  ]);
});

// ==============================================
// FALLBACK ROUTE
// ==============================================

Route::fallback(function () {
  return response()->json([
    'error' => 'Endpoint tidak ditemukan',
    'message' => 'Silakan gunakan endpoint yang tersedia',
    'available_endpoints' => [
      'GET /api/test',
      'GET /api/api-status',
      'GET /api/jdih/health',
      'GET /api/jdih/search',
      'GET /api/jdih/documents/{id}',
      'GET /api/jdih/statistics',
      'GET /api/jdih/document-types',
      'GET /api/jdih/all-documents',
      'GET /api/jdih/jdihn-format/search',
      'GET /api/jdih/jdihn-format/documents/{id}',
      'GET /api/jdih/jdihn-format/all'
    ]
  ], 404);
});
