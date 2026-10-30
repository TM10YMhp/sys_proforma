<?php

namespace App\Providers;

use App\Modules\Product\Domain\ProductRepository;
use App\Modules\Product\Infrastructure\EloquentProductRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // TODO: deberia ser singleton?
        $this->app->bind(
            ProductRepository::class,
            EloquentProductRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // if (config('app.env') === 'production' || str_contains(request()->url(), 'zrok.io')) {
        //     URL::forceScheme('https');
        // }

        // NOTE: Obtener el host (localhost, 127.0.0.1 o sysproforma.shares.zrok.io)
        // y si no es localhost ni la ip local, forzar https para zrok
        $host = Request::getHost();
        if ($host !== 'localhost' && $host !== '127.0.0.1') {
            URL::forceScheme('https');
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn(): ?Password => app()->isProduction()
                ? Password::min(12)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
                : null,
        );
    }
}
