<?php namespace Mrabbani\ModuleManager\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait ModuleInfoTrait 
{
    /**
     * @var array
     */
    protected $moduleInformation;

    /**
     * Get root folder of every modules by module type
     * @param array $type
     * @return string
     */
    protected function resolveModuleRootFolder($module)
    {
        switch (Arr::get($module, 'type')) {
            case config('module_manager.plugin_directory'):
                $path = plugins_base_path();
                break;
            case config('module_manager.module_directory'):
                $path = module_base_path();
                break;
            default:
                $path = module_base_path();
                break;
        }
        if (! Str::endsWith($path, '/')) {
            $path .= '/';
        }

        return $path;
    }

    /**
     * Current module information
     * @return array
     */
    protected function getCurrentModule()
    {
        $alias = $this->argument('alias');

        $module = get_module_information($alias);

        if(!$module) {
            $this->error('Module not exists');
            die();
        }

        $moduleRootFolder = $this->resolveModuleRootFolder($module);

        return $this->moduleInformation = array_merge($module, [
            'module-path' => $moduleRootFolder . basename(str_replace('/module.json', '', $module['file'])) . '/'
        ]);
    }

    /**
     * Get module information by key
     * @param $key
     * @return array|mixed
     */
    protected function getModuleInfo($key = null)
    {
        if (!$this->moduleInformation) {
            $this->getCurrentModule();
        }
        if (!$key) {
            return $this->moduleInformation;
        }
        return Arr::get($this->moduleInformation, $key, null);
    }
}
