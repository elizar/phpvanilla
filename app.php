<?php

class App
{
    private $routes = array();
    
    function __construct()
    {
        // Todo: write some awesome shit in here
    }
    
    function run($method, $path)
    {
        $path_segments  = $this->createSegmentsFromPath($path);
        $segments_count = count($path_segments);
        $request_method = strtolower($method);
        
        // set initial params and handler
        $_params = [];
        $matched_handler = [$this, 'notFoundHandler'];
        
        if (isset($this->routes[$request_method][$segments_count])) {
            $paths = $this->routes[$request_method][$segments_count];
            
            foreach ($paths as $route => $handler) {
                $route_segments = $this->createSegmentsFromPath($route);
                
                $segments = [];
                $params   = [];
                
                foreach ($route_segments as $seg) {
                    if (substr($seg, 0, 1) == ':') {
                        $params[str_replace(':', '', $seg)] = $path_segments[array_search($seg, $route_segments)];
                    } else {
                        $segments[] = $seg;
                    }
                }
                
                $matched_segments = array_filter($segments,
                function ($segment)
                use ($path_segments, $route_segments) {
                    return $segment == $path_segments[
                        array_search($segment, $route_segments)
                    ];
                });
                
                if (count($matched_segments) == count($segments)) {
                    $matched_handler = $handler;
                }
                
                if (count($params) > 0) {
                    $_params = $params;
                }
            }
        }
        
        return $matched_handler($_params);
    }
    
    function get($path, $handler)
    {
        $this->addRoute('get', $path, $handler);
    }
    
    function getRoutes()
    {
        return $this->routes;
    }
    
    private function notFoundHandler()
    {
        echo 'Oh Snap! - We couldn\'t find what you\'re looking for';
        echo '<br>';
        echo 'click <a href="/" title="Home">here</a> to go back.';
    }
    
    private function addRoute($method, $path, $handler)
    {
        $segments = count($this->createSegmentsFromPath($path));
        $segments = $segments == 0 ? 1 : $segments;

        if (isset($this->routes[$method][$segments][$path]) &&
            is_callable($this->routes[$method][$segments][$path])
        ) {
            return;
        }
        
        $this->routes[$method][$segments][$path] = $handler;
    }
    
    private function createSegmentsFromPath($path)
    {
        return explode('/',preg_replace('/(^\/|\/$)/','', $path));
    }
}