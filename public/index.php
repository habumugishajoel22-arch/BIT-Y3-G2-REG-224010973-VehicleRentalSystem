<?php
declare(strict_types=1);

// ---- Bootstrap: session + autoload ----
session_set_cookie_params([
    'httponly' => true,   // JavaScript cannot read the session cookie
    'samesite' => 'Lax',  // extra protection against CSRF
]);
session_start();

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../core/helpers.php';

spl_autoload_register(function (string $class): void {
    foreach (['core', 'models', 'controllers'] as $dir) {
        $file = __DIR__ . '/../' . $dir . '/' . $class . '.php';
        if (is_file($file)) { require $file; return; }
    }
});

// ---- Route table: page => [controller, [HTTP method => action], access] ----
// access: null = public, 'auth' = any logged-in user, 'admin' = admin only
$routes = [
    'login'     => ['AuthController', ['GET' => 'showLogin', 'POST' => 'login'], null],
    'register'  => ['AuthController', ['GET' => 'showRegister', 'POST' => 'register'], null],
    'logout'    => ['AuthController', ['POST' => 'logout'], 'auth'],
    'dashboard' => ['DashboardController', ['GET' => 'index'], 'auth'],

    'vehicles'        => ['VehicleController', ['GET' => 'index'], 'auth'],
    'vehicle_create'  => ['VehicleController', ['GET' => 'createForm', 'POST' => 'store'], 'auth'],
    'vehicle_edit'    => ['VehicleController', ['GET' => 'editForm', 'POST' => 'update'], 'auth'],
    'vehicle_delete'  => ['VehicleController', ['POST' => 'delete'], 'auth'],

    'customers'       => ['CustomerController', ['GET' => 'index'], 'auth'],
    'customer_create' => ['CustomerController', ['GET' => 'createForm', 'POST' => 'store'], 'auth'],
    'customer_edit'   => ['CustomerController', ['GET' => 'editForm', 'POST' => 'update'], 'auth'],
    'customer_delete' => ['CustomerController', ['POST' => 'delete'], 'auth'],

    'rentals'         => ['RentalController', ['GET' => 'index'], 'auth'],
    'rental_create'   => ['RentalController', ['GET' => 'createForm', 'POST' => 'store'], 'auth'],
    'rental_edit'     => ['RentalController', ['GET' => 'editForm', 'POST' => 'update'], 'auth'],
    'rental_return'   => ['RentalController', ['POST' => 'returnVehicle'], 'auth'],
    'rental_cancel'   => ['RentalController', ['POST' => 'cancel'], 'auth'],
    'rental_delete'   => ['RentalController', ['POST' => 'delete'], 'admin'],

    'users'           => ['UserController', ['GET' => 'index'], 'admin'],
    'user_create'     => ['UserController', ['GET' => 'createForm', 'POST' => 'store'], 'admin'],
    'user_edit'       => ['UserController', ['GET' => 'editForm', 'POST' => 'update'], 'admin'],
    'user_delete'     => ['UserController', ['POST' => 'delete'], 'admin'],

    'api_vehicles'    => ['ApiController', ['GET' => 'vehicles'], 'api'],
    'api_customers'   => ['ApiController', ['GET' => 'customers'], 'api'],
];

$page   = (string) ($_GET['page'] ?? 'dashboard');
$method = $_SERVER['REQUEST_METHOD'];

if (!isset($routes[$page])) { abort(404, 'Page not found'); }
[$controller, $actions, $access] = $routes[$page];
if (!isset($actions[$method])) { abort(405, 'Method not allowed'); }

if ($access === 'api' && !Auth::check()) {            // JSON endpoints answer with JSON errors
    http_response_code(401);
    header('Content-Type: application/json');
    exit(json_encode(['error' => 'Not logged in']));
}
if ($access === 'auth')  { Auth::requireLogin(); }
if ($access === 'admin') { Auth::requireRole('admin'); }

(new $controller())->{$actions[$method]}();
