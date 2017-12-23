<?php

// This router is script is for the built-in PHP web server. 
// The built-in web server will, be default, try to load router.js 
// as a static file instead of calling the Slim front controller.
$_SERVER['SCRIPT_NAME'] = '/index.php';
include __DIR__ . DIRECTORY_SEPARATOR . 'index.php';
