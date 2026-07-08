<?php namespace Mrabbani\ModuleManager\Providers;

use Illuminate\Support\Arr;
use \Illuminate\Support\ServiceProvider;
use Mrabbani\ModuleManager\Support\ModulesManagement;

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
           if (Arr::get($module, 'installed', null) === true) {
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

        app(ModulesManagement::class)->setModules($this->modules);
    }
}
