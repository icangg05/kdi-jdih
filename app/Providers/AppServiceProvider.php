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

        // Backend memuat Bootstrap 3.4.1, jadi paginator harus pakai markup BS3.
        // useBootstrapFive() bikin dua blok (mobile d-sm-none + desktop d-none d-sm-flex)
        // yang class-nya tidak ada di BS3, sehingga kedua blok tampil (dobel pagination).
        Paginator::useBootstrapThree();

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
