<?php

namespace App\Action;

use AndrewCarterUK\APOD\APIInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Stratigility\MiddlewareInterface;

class PictureListAction implements MiddlewareInterface
{
    private $apodApi;

    private $resultsPerPage;

    public function __construct(APIInterface $apodApi, $resultsPerPage)
    {
        $this->apodApi = $apodApi;
        $this->resultsPerPage = $resultsPerPage;
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
        $page = intval($request->getAttribute('page')) ?: 0;
        $pictures = $this->apodApi->getPage($page, $this->resultsPerPage);
        $response->getBody()->write(json_encode($pictures));

        return $response
//            ->withHeader('Cache-Control', [ 'public', 'max-age=3600'])
            ->withHeader('Content-Type', 'application/json');
    }
}
