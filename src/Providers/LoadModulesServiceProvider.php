<?php namespace Mrabbani\ModuleManager\Providers;

use Closure;
use \Illuminate\Support\ServiceProvider;

class LoadModulesServiceProvider extends ServiceProvider
{
    /**
     * Modules information
     * @var array
     */
    protected $modules = [];

    protected $notLoadedModules = [];

    public function register()
    {
        $this->modules = get_all_module_information();

        foreach ($this->modules as $module) {
            $needToBootstrap = false;
           if (array_get($module, 'installed', null) === true) {
                $needToBootstrap = true;
            }
            if ($needToBootstrap) {
                /**
                 * Register module
                 */
                $moduleProvider = $module['namespace'] . '\Providers\ModuleProvider';

                if (class_exists($moduleProvider)) {
                    $this->app->register($moduleProvider);
                } else {
                    $this->notLoadedModules[] = $moduleProvider;
                }
            }
        }
    }

    public function boot()
    {
        app()->booted(function () {
            $this->booted(static function() {
                
            });
        });
    }

    public function booted(Closure $callback)
    {
       \ModulesManagement::setModules($this->modules);
        $this->bootedCallbacks[] = $callback;
    }
}
