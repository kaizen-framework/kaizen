<?php

declare(strict_types=1);

namespace Kaizen\Components\HttpKernel;

use Kaizen\Components\HttpKernel\HttpFoundation\JsonResponse;
use Kaizen\Components\HttpKernel\HttpFoundation\Request;
use Kaizen\Components\HttpKernel\HttpFoundation\Response;
use Kaizen\Components\HttpKernel\HttpFoundation\ResponseCode;
use Kaizen\Components\HttpKernel\Router\Router;

readonly class HttpKernel
{
    public function __construct(
        private Router $router,
    ) {}

    public function init(?callable $exceptionHandler = null, ?callable $errorHandler = null): void
    {
        $this->initErrorHandling($errorHandler);
        $this->initExceptionHandling($exceptionHandler);
    }

    public function initErrorHandling(?callable $handler): void
    {
        $handler ??= $this->defaultErrorHandler(...);
        set_error_handler($handler);
    }

    public function defaultErrorHandler(int $code, string $message, string $file, int $line): bool
    {
        var_dump($code);
        var_dump($message);
        var_dump($file);
        var_dump($line);

        return true;
    }

    private function initExceptionHandling(?callable $handler): void
    {
        $handler ??= $this->defaultExceptionHandler(...);
        set_exception_handler(ExceptionDebuggerHandler::handle(...));
    }

    public function defaultExceptionHandler(\Exception $e): bool
    {
        $fileObject = new \SplFileObject($e->getFile());
        $fileObject->seek($e->getLine() - 6);

        $lastTraces = array_reverse($e->getTrace());

        echo "<pre>";
        echo "<p>{$fileObject->fgets()}</p>";
        echo "<p>{$fileObject->fgets()}</p>";
        echo "<p>{$fileObject->fgets()}</p>";
        echo "<p>{$fileObject->fgets()}</p>";
        echo "<p>{$fileObject->fgets()}</p>";
        echo "<p style='background: rgba(255, 0, 0, .6)'>{$fileObject->fgets()}</p>";
        echo "<p>{$fileObject->fgets()}</p>";
        echo "<p>{$fileObject->fgets()}</p>";
        echo "<p>{$fileObject->fgets()}</p>";
        echo "<p>{$fileObject->fgets()}</p>";
        echo "<p>{$fileObject->fgets()}</p>";

        return true;
    }

    public function handle(Request $request): Response
    {
        $route = $this->router->getRouteByUri($request->getUri());

        if (!$route) {
            throw new \Exception();
        }

        try {
            return ($route->controller)();
        } catch (\Throwable $e) {
            return new JsonResponse([
                'message' => $e->getMessage()
            ], ResponseCode::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
