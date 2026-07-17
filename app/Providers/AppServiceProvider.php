<?php

namespace App\Providers;

use App\Models\DataLampiran;
use App\Models\Document;
use App\Models\Pengumuman;
use App\Observers\DataLampiranObserver;
use App\Observers\DocumentObserver;
use App\Observers\PengumumanObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Tidak perlu register Telescope jika tidak terinstall
        
        // Bind repository atau service patterns jika diperlukan
        // $this->registerRepositories();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix untuk MariaDB/MySQL lama
        Schema::defaultStringLength(191);
        
        // Force HTTP scheme untuk local development
        if ($this->app->environment('local') || $this->app->environment('development')) {
            URL::forceScheme('http');
            
            // Nonaktifkan secure cookie di local
            config([
                'session.secure' => false,
                'session.same_site' => 'lax',
            ]);
        }
        // Force HTTPS di production
        elseif ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Upgrade ke Bootstrap 5 jika menggunakan Bootstrap 5
        // Jika masih Bootstrap 3/4, gunakan yang sesuai
        Paginator::useBootstrapFive(); // atau useBootstrapThree() / useBootstrapFour()
        
        // Jika muncul error, ganti dengan:
        // Paginator::useBootstrap();

        // Register model observers
        Pengumuman::observe(PengumumanObserver::class);
        Document::observe(DocumentObserver::class);
        DataLampiran::observe(DataLampiranObserver::class);
    }
    
    /**
     * Register repository bindings (Repository Pattern)
     * HAPUS atau COMMENT jika belum implement Repository Pattern
     */
    /*
    protected function registerRepositories(): void
    {
        $this->app->bind(
            \App\Contracts\DocumentRepositoryInterface::class,
            \App\Repositories\DocumentRepository::class
        );
        
        $this->app->bind(
            \App\Contracts\ApiRepositoryInterface::class,
            \App\Repositories\ApiRepository::class
        );
        
        $this->app->singleton('jdih.api', function ($app) {
            return new \App\Services\JdihApiService();
        });
    }
    */
}