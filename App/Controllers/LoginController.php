<?php

namespace App\Controllers;

use App\Config;
use App\Controllers\LanguageController;

class LoginController {
    public static function index() {
        require __DIR__ . '/../../views/login.php';
    }

    public static function isLogged() {
        $username = $_SESSION['username'] ?? null;
        $password = $_SESSION['password'] ?? null;

        $users = Config::get('USERS', []);

        foreach ($users as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                return true;
            }
        }
        
        return false;
    }

    public static function submit() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $users = Config::get('USERS', []);

        $userFound = false;

        foreach ($users as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                $userFound = true;
                break;
            }
        }

        if ($userFound) {
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;

            header("Location: /");
            exit;
        } else {
          $_SESSION['accessDenied'] = LanguageController::translate('Access denied');
          self::index();
        }
    }

    public static function logout() {
        session_destroy();
        header("Location: /");
        exit;
    }
}
