<?php

declare(strict_types=1);

namespace Kaizen\Components\HttpKernel\HttpFoundation;

use Kaizen\Components\HttpKernel\HttpFoundation\Bag\InputBag;
use Kaizen\Components\HttpKernel\HttpFoundation\Bag\ParameterBag;
use Kaizen\Components\HttpKernel\HttpFoundation\Constant\RequestMethod;

readonly class Request
{
    private function __construct(
        private string $uri,
        private RequestMethod $method,
        private ParameterBag $server,
        private ParameterBag $getParameters,
        private ParameterBag $postParameters
    ) {}

    public static function createFromGlobals(): self
    {
        $uri = $_SERVER['REQUEST_URI'];
        $method = RequestMethod::from($_SERVER['REQUEST_METHOD']);

        return new self($uri, $method, new ParameterBag($_SERVER), new ParameterBag($_GET), new ParameterBag($_POST));
    }

    /**
     * @param array<string, string> $postParameters
     * @param array<string, string> $getParameters
     * @param array<string, string> $server
     */
    public static function create(
        string $uri,
        RequestMethod $method,
        array $postParameters = [],
        array $getParameters = [],
        array $server = []
    ): self {
        $server = array_replace([
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 80,
            'HTTP_HOST' => 'localhost',
            'HTTP_USER_AGENT' => 'App',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5',
            'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'REMOTE_ADDR' => '127.0.0.1',
            'SCRIPT_NAME' => '',
            'SCRIPT_FILENAME' => '',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_TIME' => time(),
            'REQUEST_TIME_FLOAT' => microtime(true),
        ], $server);

        return new self(
            $uri,
            $method,
            new ParameterBag($server),
            new ParameterBag($getParameters),
            new InputBag($postParameters)
        );
    }

    public function getHttpMethod(): RequestMethod
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getParameter(string $key, mixed $default = null): mixed
    {
        if ($this->getParameters->has($key)) {
            return $this->getParameters->get($key);
        }

        if ($this->postParameters->has($key)) {
            return $this->postParameters->get($key);
        }

        return $default;
    }

    public function getGetParameters(): ParameterBag
    {
        return $this->getParameters;
    }

    public function getPostParameters(): ParameterBag
    {
        return $this->postParameters;
    }

    public function server(): ParameterBag
    {
        return $this->server;
    }

    public function headers(): ParameterBag
    {
        return $this->server;
    }
}
