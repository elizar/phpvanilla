<?php

class App
{
    private $routes = array();
    public $notFoundHandler;

    function __construct()
    {
        $this->notFoundHandler = [$this, 'defaultNotFoundHandler'];
    }

    function get($path, $handler)
    {
        return $this->addRoute('get', $path, $handler);
    }

    function post($path, $handler)
    {
        return $this->addRoute('post', $path, $handler);
    }

    function run($method, $path)
    {
        $request_segments  = $this->createSegmentsFromPath($path);
        $segments_count = count($request_segments);
        $request_method = strtolower($method);

        // Set initial request params
        $params = [];
        // Set initial notFound handler
        $matched_handler = $this->notFoundHandler;

        if (isset($this->routes[$request_method][$segments_count])) {
            $paths = $this->routes[$request_method][$segments_count];

            foreach ($paths as $route => $handler) {
                $route_segments = $this->createSegmentsFromPath($route);

                $segments = $this->getSegmentsFromRouteSegments($route_segments);
                // Check if matched, then set $matched_handler
                if ($this->segmentsMatched($segments, $route_segments, $request_segments)) {
                    $matched_handler = $handler;
                }

                // Set request params
                $params = $this->getParamsFromSegments($route_segments, $request_segments);
            }
        }

        return $matched_handler($params);
    }

    /**
     * -------------------------------------------------------------
     * PRIVATE METHODS
     * -------------------------------------------------------------
     *
     */
    private function defaultNotFoundHandler()
    {
        echo 'Oh Snap! - We couldn\'t find what you\'re looking for';
        echo '<br>';
        echo 'click <a href="/" title="Home">here</a> to go back.';
    }

    /*
     * Adds route handler
     *
     * @param string $method
     * @param string $path
     * @param function $handler
     * @return self for chainability
     */
    private function addRoute($method, $path, $handler)
    {
        $segments = count($this->createSegmentsFromPath($path));
        $segments = $segments == 0 ? 1 : $segments;

        if (isset($this->routes[$method][$segments][$path]) &&
            is_callable($this->routes[$method][$segments][$path])
        ) {
            return $this;
        }

        $this->routes[$method][$segments][$path] = $handler;
        // return self for chaining
        return $this;
    }

    /*
     * Transform a path into an array of segments
     *
     * @param string $path
     * @return array
     */
    private function createSegmentsFromPath($path)
    {
        return explode('/',preg_replace('/(^\/|\/$)/','', $path));
    }

    /*
     * Gets non parameter segments from route's segments
     *
     * Given the following route `/users/:id/friends`,
     * creates ['users', ':id', 'friends'] segments array.
     *
     * This method will filter those non-parameter segment and
     * return only those segments i.e ['users', 'friends']
     *
     * @param array $route_segments An array of segments from route
     * @return array A list of non-parameter segments
     */
    private function getSegmentsFromRouteSegments($route_segments)
    {
        $segments = [];

        foreach ($route_segments as $segment) {
            if (substr($segment, 0, 1) != ':') {
                $segments[] = $segment;
            }
        }

        return $segments;
    }

    /*
     * Gets all the param segments from route and
     * match it up with param values from request
     * path.
     *
     * Consider the folowing request path:
     * `/user/31337/friend/12345`
     * and the matching route's path:
     * `/user/:id/friend/:fid`
     *
     * Results: ['id' => 31337, 'fid' => 12345]
     *
     * @param array $route_segments
     * @param array $request_segments
     * @return array
     */
    private function getParamsFromSegments($route_segments, $request_segments)
    {
        $params = [];

        foreach ($route_segments as $segment) {
            if (substr($segment, 0, 1) == ':') {
                $index = array_search($segment, $route_segments);
                $params[str_replace(':', '', $segment)] = $request_segments[$index];
            }
        }

        return $params;
    }

    /*
     * Validates if a selected array of segments
     * matches the current request's segments
     *
     * @param array $segments
     * @param array $route_segments
     * @param array $request_segments
     * @return bool
     */
    private function segmentsMatched($segments, $route_segments, $request_segments)
    {
        $matched_segments = array_filter(
            $segments,
            function ($segment) use ($request_segments, $route_segments) {
                $index = array_search($segment, $route_segments);
                // include only if it matches
                return $segment == $request_segments[$index];
            }
        );

        if (count($matched_segments) == count($segments)) {
            return true;
        }

        return false;
    }
}
