<?php

namespace Llvdl\Slim;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Router;
use Slim\Http\Stream;
use Slim\Http\Response;
use Llvdl\Slim\Middleware\Pattern\PatternParser;
use Llvdl\Slim\Middleware\Pattern\PartInterface;
use FastRoute\RouteParser\Std;

/**
 * Slim Router JS
 *
 * @author      Lennaert van der Linden <lennaertvanderlinden@gmail.com>
 * @copyright   2017 Lennaert van der Linden
 * @link        http://github.com/llvdl/slim-router-js
 * @license     MIT
 * @version     1.0
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class RouterJs
{
	const MINIFIED_JS_TEMPLATE = 'route.min.js.php';
	const JS_TEMPLATE = 'route.js.php';
	
    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $jsPackageName = 'Slim';

    /**
     * @var string
     */
    private $jsObjectName = 'Router';
    
    /**
     * @var bool
     */
    private $minifiedJs;

    /**
     * @param Router
     */
    public function __construct(Router $router, $minified = true)
    {
        $this->router = $router;
        $this->minifiedJs = $minified;
    }

    /**
     * Returns a Response object for returning the router Javascript code as HTTP response.
     */
    public function getRouterJavascriptResponse()
    {

        $fh = fopen('php://temp', 'rw');
        $stream = new Stream($fh);

        $stream->write($this->getRouterJavascript());

        return (new Response())
            ->withBody($stream)
            ->withHeader('Content-Type', 'application/javascript');
    }

    /**
     * Generates the javascript code for the Router class
     *
     * @return string
     */
    public function getRouterJavascript()
    {
        return $this->createRouteJs([
            'basePath' => $this->getBasePath(),
            'routes' => $this->getParsedRoutes()
        ]);
    }
    
    /**
     * return array
     */
    private function getParsedRoutes()
    {
        $parser = new Std();

        $routes = [];
        foreach ($this->router->getRoutes() as $route) {
            if ($route->getName()) {
                $routes[$route->getName()] = $parser->parse($route->getPattern());
            }
        }
        
        return $routes;
	}

    /**
     * @var array $variables
     */
    private function createRouteJs(array $variables)
    {
        extract($variables);

        ob_start();
        include $this->getJsTemplateFile();

        return ob_get_clean();
    }
    
    /**
     * @return string
     */
    private function getJsTemplateFile()
    {
		$template = ($this->minifiedJs ? self::MINIFIED_JS_TEMPLATE : SELF::JS_TEMPLATE);
		
		return sprintf('%s/template/%s', __DIR__, $template);
	}

    /**
     * @return string
     */
    private function getBasePath()
    {
        // base path is protected
        $prop = new \ReflectionProperty(get_class($this->router), 'basePath');
        $prop->setAccessible(true);

        return $prop->getValue($this->router);
    }
}
