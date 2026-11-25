<?php

namespace App\Providers;

use App\Certificates\CertificateCategoryModuleRegistry;
use App\Certificates\Contracts\CertificateCategoryModule;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use App\Models\Certificate;
use App\Models\Claim;
use App\Observers\CertificateObserver;
use App\Services\Academy\AcademyTodoService;
use Illuminate\Support\Facades\View;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CertificateCategoryModuleRegistry::class, function ($app) {
            $config = config('certificates');
            $moduleClasses = $config['modules'] ?? [];
            $fallbackClass = $config['module_fallback'];

            /** @var CertificateCategoryModule $fallback */
            $fallback = $app->make($fallbackClass);

            $modules = [];
            foreach ($moduleClasses as $moduleClass) {
                $module = $app->make($moduleClass);
                if ($module instanceof CertificateCategoryModule) {
                    $modules[] = $module;
                }
            }

            if (!collect($modules)->contains(fn (CertificateCategoryModule $module) => $module->key() === $fallback->key())) {
                $modules[] = $fallback;
            }

            return new CertificateCategoryModuleRegistry($modules, $fallback);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // Register Certificate Observer
        Certificate::observe(CertificateObserver::class);

        // Share claim counts with the menu
        View::composer('partials.menu', function ($view) {
            if (auth()->check()) {
                $openClaimsCount = Claim::whereNotIn('status', ['finished', 'draft', 'draft_denied'])->count();
                $conceptClaimsCount = Claim::whereIn('status', ['draft', 'draft_denied'])->count();
                $view->with(compact('openClaimsCount', 'conceptClaimsCount'));

                if (auth()->user()->can('academy_access')) {
                    $view->with('academyOverdueCount', app(AcademyTodoService::class)->overdueCount());
                }
            }
        });

        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
    
            return new LengthAwarePaginator($this->forPage($page, $perPage), $total ?: $this->count(), $perPage, $page, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]);
        });
    }
}
