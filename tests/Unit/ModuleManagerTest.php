<?php

namespace Mrabbani\Tests\Unit;

use Mrabbani\ModuleManager\Console\Commands\DisableModuleCommand;
use Mrabbani\ModuleManager\Console\Commands\EnableModuleCommand;
use Mrabbani\ModuleManager\Console\Commands\InstallModuleCommand;
use Mrabbani\ModuleManager\Console\Commands\ModuleSeedCommand;
use Mrabbani\ModuleManager\Console\Commands\RouteListCommand;
use Mrabbani\ModuleManager\Console\Commands\UninstallModuleCommand;
use Mrabbani\ModuleManager\Console\Generators\MakeCommand;
use Mrabbani\ModuleManager\Console\Generators\MakeController;
use Mrabbani\ModuleManager\Console\Generators\MakeFacade;
use Mrabbani\ModuleManager\Console\Generators\MakeMigration;
use Mrabbani\ModuleManager\Console\Generators\MakeMiddleware;
use Mrabbani\ModuleManager\Console\Generators\MakeModel;
use Mrabbani\ModuleManager\Console\Generators\MakeModule;
use Mrabbani\ModuleManager\Console\Generators\MakeProvider;
use Mrabbani\ModuleManager\Console\Generators\MakeRequest;
use Mrabbani\ModuleManager\Console\Generators\MakeSeeder;
use Mrabbani\ModuleManager\Console\Generators\MakeService;
use Mrabbani\ModuleManager\Console\Migrations\ModuleMigrateCommand;
use Mrabbani\ModuleManager\Console\Migrations\RollbackCommand;
use PHPUnit\Framework\TestCase;

class ModuleManagerTest extends TestCase
{
    public function testPublicCommandNamesRemainStable(): void
    {
        $this->assertSame('module:create', $this->commandName(MakeModule::class));
        $this->assertSame('module:make:controller', $this->commandName(MakeController::class));
        $this->assertSame('module:make:command', $this->commandName(MakeCommand::class));
        $this->assertSame('module:make:facade', $this->commandName(MakeFacade::class));
        $this->assertSame('module:make:middleware', $this->commandName(MakeMiddleware::class));
        $this->assertSame('module:make:migration', $this->commandName(MakeMigration::class));
        $this->assertSame('module:make:model', $this->commandName(MakeModel::class));
        $this->assertSame('module:make:provider', $this->commandName(MakeProvider::class));
        $this->assertSame('module:make:request', $this->commandName(MakeRequest::class));
        $this->assertSame('module:make:service', $this->commandName(MakeService::class));
        $this->assertSame('module:make:seeder', $this->commandName(MakeSeeder::class));
        $this->assertSame('module:migrate', $this->commandName(ModuleMigrateCommand::class));
        $this->assertSame('module:migrate:rollback', $this->commandName(RollbackCommand::class));
        $this->assertSame('module:install', $this->commandName(InstallModuleCommand::class));
        $this->assertSame('module:uninstall', $this->commandName(UninstallModuleCommand::class));
        $this->assertSame('module:enable', $this->commandName(EnableModuleCommand::class));
        $this->assertSame('module:disable', $this->commandName(DisableModuleCommand::class));
        $this->assertSame('module:db:seed', $this->commandName(ModuleSeedCommand::class));
        $this->assertSame('module:routes', $this->routeCommandName(RouteListCommand::class));
    }

    private function commandName(string $class): string
    {
        $ref = new \ReflectionClass($class);
        $property = $ref->getProperty('signature');
        $property->setAccessible(true);

        $signature = preg_replace('/\s+/', ' ', trim((string) $property->getValue($ref->newInstanceWithoutConstructor())));

        return strtok($signature, ' ') ?: $signature;
    }

    private function routeCommandName(string $class): string
    {
        $ref = new \ReflectionClass($class);
        $property = $ref->getProperty('name');
        $property->setAccessible(true);

        return (string) $property->getValue($ref->newInstanceWithoutConstructor());
    }
}
