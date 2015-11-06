<?php

namespace App\Middleware;

use Doctrine\Common\Cache\Cache;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Stratigility\MiddlewareInterface;

class CacheMiddleware implements MiddlewareInterface
{
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Process an incoming request and/or response.
     *
     * Accepts a server-side request and a response instance, and does
     * something with them.
     *
     * If the response is not complete and/or further processing would not
     * interfere with the work done in the middleware, or if the middleware
     * wants to delegate to another process, it can use the `$out` callable
     * if present.
     *
     * If the middleware does not return a value, execution of the current
     * request is considered complete, and the response instance provided will
     * be considered the response to return.
     *
     * Alternately, the middleware may return a response instance.
     *
     * Often, middleware will `return $out();`, with the assumption that a
     * later middleware will return a response.
     *
     * @param Request $request
     * @param Response $response
     * @param null|callable $out
     * @return null|Response
     */
    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        // TODO: Implement __invoke() method.
    }

    private function getCacheKey(ServerRequestInterface $request)
    {
        return 'http-cache:' . $request->getUri()->getPath();
    }

    private function getCachedResponse(ServerRequestInterface $request, ResponseInterface $response)
    {
        if ('GET' === $request->getMethod()) {
            return null;
        }

        $item = $this->cache->fetch($this->getCacheKey($request));

        if (false == $item) {
            return null;
        }

        $response->getBody()->write($item['body']);

        foreach($item['headers'] as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }

    private function cacheResponse(ServerRequestInterface $request, ResponseInterface $response)
    {
        if ('GET' !== $request->getMethod() || !$response->hasHeader('Cache-Control')) {
            return;
        }

        $cacheControl = $response->getHeader('Cache-Control');

        $abortTokens = array('private', 'no-cache', 'no-store');

        if (count(array_intersect($abortTokens, $cacheControl)) > 0) {
            return;
        }

        foreach($cacheControl as $value) {
            $parts = explode('=', $value);

            if (count($parts) == 2 && 'max-age' === $parts[0]) {
                $this->cache->save($this->getCacheKey($request), [
                    'body' => (string) $response->getBody(),
                    'headers' => $response->getHeaders(),
                ], intval($parts[1]));
            }

            return;
        }
    }
}
