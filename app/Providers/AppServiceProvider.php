<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\RhFeuilleDeTempsReguliere\Livewire\RhFeuilleDeTempsReguliereManagerDashboard;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enregistrement manuel du composant
        Livewire::component('rh-feuille-de-temps-reguliere-manager-dashboard', RhFeuilleDeTempsReguliereManagerDashboard::class);
    }
}


/*
    <?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Activer le mode strict pour Eloquent (détecte les lazy loading, etc.)
        if ($this->app->environment('local')) {
            Model::shouldBeStrict();
            
            // Ou individuellement :
            // Model::preventLazyLoading();
            // Model::preventSilentlyDiscardingAttributes();
            // Model::preventAccessingMissingAttributes();
        }

        // Activer les logs stricts pour les requêtes
        if ($this->app->environment('local')) {
            DB::listen(function ($query) {
                if ($query->time > 1000) { // Plus de 1 seconde
                    logger()->warning('Slow query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time
                    ]);
                }
            });
        }

        // Vérifier les vues manquantes
        View::composer('*', function ($view) {
            // Log des variables non utilisées dans les vues
            if (config('app.debug')) {
                $data = $view->getData();
                foreach ($data as $key => $value) {
                    if (is_null($value) && !in_array($key, ['errors', '__env', 'app'])) {
                        logger()->notice("Null variable in view: {$key}", [
                            'view' => $view->name(),
                            'variable' => $key
                        ]);
                    }
                }
            }
        });
    }
}
*/
