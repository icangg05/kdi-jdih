<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\JdihKendariApiController;

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

Route::get('/test', function() {
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
// SIMPLE DIRECT ROUTES (untuk testing cepat)
// ==============================================

// Route untuk testing langsung tanpa prefix
Route::get('/api-status', function() {
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

Route::fallback(function() {
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
