<?php

namespace Llvdl\Slim\Tests;

use PHPUnit\Framework\TestCase;
use Llvdl\Slim\RouterJs;

class RouterJsTest extends TestCase
{
    /**
     * @test
     */
    public function itProvidesRouterJavascriptCode()
    {
        $router = new \Slim\Router();
        $routerJs = new RouterJs($router);

        $javascriptCode = $routerJs->getRouterJavascript();

        // the Javascript code is not tested for validity here
        $this->assertContains('Slim.Router', $javascriptCode);
    }

    /**
     * @test
     */
    public function itProvidesNonMinifiedRouterJavascriptCode()
    {
        $router = new \Slim\Router();
        $routerJs = new RouterJs($router, false);

        $javascriptCode = $routerJs->getRouterJavascript();

        // the Javascript code is not tested for validity here
        $this->assertContains('Slim.Router', $javascriptCode);
        $this->assertContains(
            '// Slim Router object to generate URLs for routes',
            $javascriptCode,
            'contains comments'
        );
    }

    /**
     * @test
     */
    public function itProvidesRouterJavascriptCodeWithRoutes()
    {
        $app = new \Slim\App();

        $app->get('/my-route', function ($req, $res, $args) {
            return 'test';
        })->setName('named_testroute');

        $app->get('/unnamed-route', function ($req, $res, $args) {
            return 'test';
        });

        $router = $app->getContainer()->get('router');
        $routerJs = new RouterJs($router);
        $javascriptCode = $routerJs->getRouterJavascript();

        // Note: the Javascript code is not tested for validity here
        $this->assertContains('Slim.Router', $javascriptCode);
        $this->assertContains('testroute', $javascriptCode, 'named testroute is included');
        $this->assertNotContains('unnamed-route', $javascriptCode, 'unnamed testroute is not included');
    }

    /**
     * @test
     */
    public function itProvidesAnHttpResponseWithJavascriptCode()
    {
        $app = new \Slim\App();

        $app->get('/my-route', function ($req, $res, $args) {
            return 'test';
        })->setName('named_testroute');

        $app->get('/unnamed-route', function ($req, $res, $args) {
            return 'test';
        });

        $router = $app->getContainer()->get('router');
        $routerJs = new RouterJs($router);
        $response = $routerJs->getRouterJavascriptResponse();

        $this->assertSame(
            ['application/javascript'],
            $response->getHeader('Content-Type'),
            'content type for javascript is set'
        );

        $javascriptCode = (string) $response->getBody();

        // Note: the Javascript code is not tested for validity here
        $this->assertContains('Slim.Router', $javascriptCode);
        $this->assertContains('testroute', $javascriptCode, 'named testroute is included');
        $this->assertNotContains('unnamed-route', $javascriptCode, 'unnamed testroute is not included');
    }
}
