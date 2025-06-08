<?php

namespace App;
use Exception;

class Config
{
    protected static $configData;

    public static function load()
    {
        $configFilePath = __DIR__ . '/../config.php';
        
        if (!file_exists($configFilePath)) {
            throw new Exception('Configuration file not found.');
        }
        
        self::$configData = require $configFilePath;
    }

    public static function get($key, $default = null)
    {
        if (!isset(self::$configData)) {
            self::load();
        }

        return isset(self::$configData[$key]) ? self::$configData[$key] : $default;
    }
}
