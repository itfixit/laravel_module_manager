<?php

namespace Mrabbani\Tests\Unit;

require_once __DIR__ . '/../../helpers/file.php';
require_once __DIR__ . '/../../helpers/helpers.php';

class ModuleHelpersTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['module_manager_test_base_path'] = sys_get_temp_dir() . '/module-manager-tests-' . uniqid('', true);
        $GLOBALS['module_manager_test_config'] = [
            'module_manager.module_directory' => 'modules',
            'module_manager.plugin_directory' => 'plugins',
        ];
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($GLOBALS['module_manager_test_base_path']);

        parent::tearDown();
    }

    public function testGetAllModuleInformationScansBothDirectories(): void
    {
        $this->writeModuleJson('modules/blog', [
            'alias' => 'blog',
            'namespace' => 'App\\Modules\\Blog',
            'installed' => true,
        ]);

        $this->writeModuleJson('plugins/chat', [
            'alias' => 'chat',
            'namespace' => 'App\\Plugins\\Chat',
            'installed' => false,
        ]);

        $modules = get_all_module_information();

        $this->assertCount(2, $modules);
        $this->assertSame(['blog', 'chat'], array_values(collect($modules)->pluck('alias')->sort()->all()));
        $this->assertSame(['modules', 'plugins'], array_values(collect($modules)->pluck('type')->sort()->all()));
    }

    public function testGetModulesByTypeMapsPluginsAliasToConfiguredPluginDirectory(): void
    {
        $this->writeModuleJson('modules/blog', [
            'alias' => 'blog',
            'namespace' => 'App\\Modules\\Blog',
            'installed' => true,
        ]);

        $this->writeModuleJson('plugins/chat', [
            'alias' => 'chat',
            'namespace' => 'App\\Plugins\\Chat',
            'installed' => false,
        ]);

        $modules = get_modules_by_type('plugins');

        $this->assertCount(1, $modules);
        $this->assertSame(['chat'], $modules->pluck('alias')->all());
    }

    private function writeModuleJson(string $relativeDirectory, array $data): void
    {
        $directory = base_path($relativeDirectory);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($directory . '/module.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
            } else {
                unlink($item->getPathname());
            }
        }

        rmdir($directory);
    }
}
