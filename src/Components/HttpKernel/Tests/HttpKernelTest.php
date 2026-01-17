<?php

declare(strict_types=1);

namespace Kaizen\Components\HttpKernel\Tests;

use Kaizen\Components\HttpKernel\AbstractController;
use Kaizen\Components\HttpKernel\HttpFoundation\Constant\RequestMethod;
use Kaizen\Components\HttpKernel\HttpFoundation\JsonResponse;
use Kaizen\Components\HttpKernel\HttpFoundation\Request;
use Kaizen\Components\HttpKernel\HttpFoundation\Response;
use Kaizen\Components\HttpKernel\HttpFoundation\ResponseCode;
use Kaizen\Components\HttpKernel\HttpKernel;
use Kaizen\Components\HttpKernel\Router\Route;
use Kaizen\Components\HttpKernel\Router\Router;
use PHPUnit\Framework\TestCase;

class HttpKernelTest extends TestCase
{
    public function testHandleReturnResponse(): void
    {
        $rootController = new class extends AbstractController {
            public function __invoke(): JsonResponse
            {
                return new JsonResponse([]);
            }
        };

        $testController = new class extends AbstractController {
            public function __invoke(): Response
            {
                return new Response('The response');
            }
        };

        $router = new Router();
        $router->registerRoute(new Route(
            'root',
            '/',
            RequestMethod::GET,
            $rootController
        ));
        $router->registerRoute(new Route(
            'test',
            '/test',
            RequestMethod::GET,
            $testController
        ));

        $httpKernel = new HttpKernel($router);

        $rootRequest = Request::create('/', RequestMethod::GET);
        $rootResponse = $httpKernel->handle($rootRequest);

        $this->assertInstanceOf(JsonResponse::class, $rootResponse);
        $this->assertSame('[]', $rootResponse->getContent());

        $testRequest = Request::create('/test', RequestMethod::GET);
        $testResponse = $httpKernel->handle($testRequest);

        $this->assertInstanceOf(Response::class, $testResponse);
        $this->assertNotInstanceOf(JsonResponse::class, $testResponse);

        $this->assertSame('The response', $testResponse->getContent());
    }

    public function testHandleWithExceptionThrown(): void
    {
        $controller = new class extends AbstractController {

            public function __invoke(): JsonResponse
            {
                throw new \Exception('Exception thrown');
            }
        };

        $router = new Router();
        $router->registerRoute(new Route(
            'root',
            '/',
            RequestMethod::GET,
            $controller
        ));

        $httpKernel = new HttpKernel($router);

        $request = Request::create('/', RequestMethod::GET);
        $response = $httpKernel->handle($request);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $expectedResponseContent = json_encode([
            'message' => 'Exception thrown',
        ]);

        $this->assertEquals($expectedResponseContent, $response->getContent());
        $this->assertSame(ResponseCode::HTTP_INTERNAL_SERVER_ERROR, $response->getResponseCode());
    }
}
