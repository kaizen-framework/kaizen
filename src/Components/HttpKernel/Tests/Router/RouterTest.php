<?php

namespace Kaizen\Components\HttpKernel\Tests\Router;

use Kaizen\Components\HttpKernel\AbstractController;
use Kaizen\Components\HttpKernel\HttpFoundation\Constant\RequestMethod;
use Kaizen\Components\HttpKernel\HttpFoundation\JsonResponse;
use Kaizen\Components\HttpKernel\Router\Route;
use Kaizen\Components\HttpKernel\Router\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testRegisterRoute(): void
    {
        $router = new Router();

        $rootController = new class extends AbstractController {
            public function __invoke(): JsonResponse
            {
                return new JsonResponse([
                    'message' => 'response'
                ]);
            }
        };

        $testController = new class extends AbstractController {
            public function __invoke(): JsonResponse
            {
                return new JsonResponse([
                    'message' => 'response'
                ]);
            }
        };

        $rootRoute = new Route(
            name: 'root',
            uri: '/',
            method: RequestMethod::GET,
            controller: $rootController,
        );

        $testRoute = new Route(
            name: 'test',
            uri: '/test',
            method: RequestMethod::GET,
            controller: $testController,
        );

        $router->registerRoute($rootRoute);
        $router->registerRoute($testRoute);

        $actualRouteByName = $router->getRouteByName('root');
        $actualRouteByIri = $router->getRouteByUri('/');

        $this->assertNotNull($actualRouteByName);
        $this->assertNotNull($actualRouteByIri);
        $this->assertSame($actualRouteByName, $actualRouteByIri);

        $this->assertSame('/', $actualRouteByIri->uri);
        $this->assertSame('root', $actualRouteByIri->name);
    }
}
