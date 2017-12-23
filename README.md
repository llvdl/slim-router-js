Slim Router JS
==============

    Generate Slim Router path URLs using Javascript.

This package provides Javascript code to generate URLs for
[named routes](https://www.slimframework.com/docs/objects/router.html#named-routes)
in [Slim Framework](https://www.slimframework.com/) using Javascript:

```javascript
var url = Slim.Router.pathFor('hello', {'name': 'World'});
```

The `Slim.Router` object provides the methods `pathFor()`
and `relativePathFor()` which work the same as the
`Slim\Router::pathFor()` and `Slim\Router:relativePathFor()` methods in PHP.

Installation
------------

Install the package using composer:

    composer require llvdl/slim-router-js

Then add a route to generate the Javascript code for the `Slim.Router` object:

```php
$app = new \Slim\App();

// Add router javascript
$app->get('/router.js', function($req, $res, $args) {
    $routerJs = new \Llvdl\Slim\RouterJs($this->router);
    return $routerJs->getRouterJavascriptResponse();
});
```

*Note:* `router.js` is considered as a static file by the PHP built-in
webserver. Either use a router script, or use a pattern without an extension,
for example '/router`. See the
[PHP documentation](http://php.net/manual/en/features.commandline.webserver.php)
for more information.

Finally, in the HTML file, import the `router.js` file:

```html
<html>
  <head>
    <script src="/router.js"></script>
  </head>
</html>
```

Usage
-----

To make a route available in `Slim.Router` in javascript, add a name to it:

```php
$app->get('/hello/{name}', function($req, $res) {
  // ...
})->setName('hello');
```

_Note:_ routes without a name are not available to `Slim.Router` in javascript.

In the HTML document, import `router.js`. The URL for the named route can then be generated using `Slim.Router.pathFor`:

```html
<html>
  <head>
    <script src="/router.js"></script>
  </head>
  <body>
    <input id="name" type="text"/>
    <button id="submit-button">Go</button>
    <script>
      document.getElementById('submit-button').on('click', function() {
        var name = document.getElementById('name').value;
        var url = Slim.Router.pathFor('hello', {name: name});
        alert(url);
      });
    </script>
  </body>
</html>
```

See the [`example/`](./example) folder in this repository for an example script.

RouterJs methods:
-----------------

RouterJs is the PHP class that generates the Javascript code. It provides
the following methods:

* `__constructor(\Slim\Router $router)`: constructor
* `getRouterJavascriptResponse(): Slim\Response`: returns a HTTP response
for use in an action
* `getRouterJavascript(): string`: generates the javascript code

Slim.Router methods
-------------------

The `Slim.Router` object provides the following methods:

* `pathFor(name, data, queryParams)`
* `relativePathFor(name, data, queryParams)`

These method work as the `Slim\Router::pathFor()` and
`Slim\Router::relativePathFor()` methods in PHP.

Todo
----

* Add tests
* Filter exposed routes, for example by route argument
* Caching
* Allow for inclusing in a Javascript bundler, for example Webpack
