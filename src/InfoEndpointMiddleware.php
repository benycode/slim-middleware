<?php

declare(strict_types=1);

namespace BenyCode\Slim\Middleware;

use Slim\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class InfoEndpointMiddleware implements MiddlewareInterface
{
    private string $infoEndpoint = '/_info';

    public function __construct(
        private array $config = [],
        private string $version = 'not_defined',
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $uri = $request
            ->getUri()
            ->getPath()
        ;

        if(isset($this->config['info_endpoint'])) {
            $this->infoEndpoint = $this->config['info_endpoint'];
        }

        if ($this->infoEndpoint === $uri) {
            $response = new Response;
            $response
                ->getBody()
                ->write(
                    \json_encode(
                        [
                            'version' => $this->version,
                            'date_time' => \date('Y-m-d H:i:s'),
                        ],
                    ),
                )
            ;

            return $response
                ->withStatus(200)
            ;
        }

        return $handler
            ->handle($request)
        ;
    }
}
