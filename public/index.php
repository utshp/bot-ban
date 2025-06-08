<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\BanlistController;
use App\Controllers\LoginController;
use App\Controllers\LanguageController;
use App\Controllers\TypeController;
use Carbon\Carbon;
use App\Config;

try {
    Config::load();
} catch (Exception $e) {
    require __DIR__ . '/../views/unconfigured.php';
    die();
}

Carbon::setLocale(LanguageController::getLocale());

if (Config::get('TIMEZONE') !== null) {
    date_default_timezone_set(Config::get('TIMEZONE'));
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/login') {
    LoginController::submit();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $path === '/logout') {
  LoginController::logout();
  exit;
}

if (in_array($path, ['/', '/bans', '/mutes', '/warnings', '/kicks'])) {
    $type = ltrim($path, '/');
} else {
    header("Location: /");
}

$type = TypeController::transformType($type);

if (LoginController::isLogged() || !Config::get('ALLOW_AUTHORIZATION', true) ) {
    BanlistController::index($type);
} else {
    LoginController::index();
}
