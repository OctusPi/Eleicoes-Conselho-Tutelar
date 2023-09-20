<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0 "); // Proxies.

require __DIR__ . '/vendor/autoload.php';

use App\Utils\Route;

$route   = new Route();
echo $route->go();
