<?php

namespace Illuminate\Routing {
    if (!class_exists(Route::class, false)) {
        class Route
        {
            public function __construct(
                private array $methods = [],
                private string $uri = '',
                private array $action = [],
                private ?string $name = null,
                private ?string $domain = null,
                private array $middleware = []
            ) {
            }

            public function domain(): ?string
            {
                return $this->domain;
            }

            public function methods(): array
            {
                return $this->methods;
            }

            public function uri(): string
            {
                return $this->uri;
            }

            public function getName(): ?string
            {
                return $this->name;
            }

            public function getActionName(): ?string
            {
                return $this->action['uses'] ?? null;
            }

            public function getAction(): array
            {
                return $this->action;
            }

            public function gatherMiddleware(): array
            {
                return $this->middleware;
            }
        }
    }
}

namespace Mrabbani\Tests\Unit {
    use Illuminate\Routing\Route;
    use Mrabbani\ModuleManager\Console\Commands\RouteListCommand;
    use PHPUnit\Framework\TestCase;
    use ReflectionMethod;
    use ReflectionProperty;

    class RouteListCommandCompatibilityTest extends TestCase
    {
        public function testRouteNamespaceIsDerivedFromControllerAction(): void
        {
            $command = new RouteListCommandForTesting();
            $route = new Route(['GET'], '/posts', ['uses' => 'App\\Modules\\Blog\\Http\\Controllers\\PostController@index']);

            $this->assertSame(
                'App\\Modules\\Blog\\Http\\Controllers\\PostController',
                $command->invokeGetRouteNamespace($route)
            );
        }

        public function testRouteNamespaceDetectionTreatsControllerPrefixAsModuleOwned(): void
        {
            $command = new RouteListCommandForTesting();
            $command->setModulesNamespaceFixture(['App\\Modules\\Blog']);

            $this->assertTrue($command->invokeIsBelongsToModule('App\\Modules\\Blog\\Http\\Controllers\\PostController'));
            $this->assertFalse($command->invokeIsBelongsToModule('App\\Http\\Controllers\\WelcomeController'));
        }
    }

    class RouteListCommandForTesting extends RouteListCommand
    {
        public function __construct()
        {
        }

        public function setModulesNamespaceFixture(array $namespaces): void
        {
            $property = new ReflectionProperty(RouteListCommand::class, 'modulesNamespace');
            $property->setAccessible(true);
            $property->setValue($this, $namespaces);
        }

        public function invokeGetRouteNamespace(Route $route): string
        {
            $method = new ReflectionMethod(RouteListCommand::class, 'getRouteNamespace');
            $method->setAccessible(true);

            return $method->invoke($this, $route);
        }

        public function invokeIsBelongsToModule(string $namespace): bool
        {
            $method = new ReflectionMethod(RouteListCommand::class, 'isBelongsToModule');
            $method->setAccessible(true);

            return $method->invoke($this, $namespace);
        }
    }
}
