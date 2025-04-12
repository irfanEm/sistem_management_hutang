<?php

require_once __DIR__ . "/../vendor/autoload.php";

use IRFANM\SIASHAF\App\Router;
use IRFANM\SIASHAF\Controller\HomeController;
use IRFANM\SIASHAF\Controller\TestController;
use IRFANM\SIASHAF\Controller\UserController;
use IRFANM\SIASHAF\Middleware\AlreadyLoginMiddleware;
use IRFANM\SIASHAF\Middleware\MustLoginMiddleware;

Router::route("GET", "/", HomeController::class, "index", []);
Router::route("GET", "/test-route", TestController::class, "index", []);
Router::route("GET", "/test-env", TestController::class, "testDotEnvLibrary", []);
Router::route("GET", "/test_db", TestController::class, "testConnDb", []);

Router::route("GET", "/admin/master/users", UserController::class, "index", [MustLoginMiddleware::class]);
Router::route("GET", "/admin/master/users/tambah", UserController::class, "register", [MustLoginMiddleware::class]);
Router::route("POST", "/admin/master/users/tambah", UserController::class, "postRegister", [MustLoginMiddleware::class]);
Router::route("GET", "/admin/master/user/detail/([0-9a-zA-Z\-\_]*)", UserController::class, "show", [MustLoginMiddleware::class]);
Router::route("GET", "/admin/master/user/ubah/([0-9a-zA-Z\-\_]*)", UserController::class, "update", [MustLoginMiddleware::class]);
Router::route("POST", "/admin/master/users/ubah", UserController::class, "postUpdate", [MustLoginMiddleware::class]);
Router::route("GET", "/admin/master/user/hapus/([0-9a-zA-Z\-\_]*)", UserController::class, "hapus", [MustLoginMiddleware::class]);

// User Route
Router::route("GET", "/users/register", UserController::class, "register", []);
Router::route("GET", "/users/login", UserController::class, "login", [AlreadyLoginMiddleware::class]);
Router::route("POST", "/users/login", UserController::class, "postLogin", [AlreadyLoginMiddleware::class]);
Router::route("GET", "/admin/beranda", UserController::class, "home", [MustLoginMiddleware::class]);
Router::route("GET", "/users/logout", UserController::class, "logout", [MustLoginMiddleware::class]);

// Beranda Route
// Router::route("GET", "/admin/master/guru", GuruController::class, "index", [MustLoginMiddleware::class]);

Router::gas();