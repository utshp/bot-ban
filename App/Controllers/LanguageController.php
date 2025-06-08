<?php

namespace App\Controllers;

use App\Config;

class LanguageController {
    private static $locale;
    private static $translations;

    private static function getTranslationsFilePath($locale) {
        return __DIR__ . '/../../lang/' . $locale . '.json';
    }

    public static function getLocale() {
        if (!isset(self::$locale)) {
            self::$locale = self::loadLocale();
        }

        return self::$locale;
    }

    public static function loadLocale() {
        $locale = Config::get('LOCALE', 'en');
        $filePath = self::getTranslationsFilePath($locale);

        if (!file_exists($filePath)) {
            $locale = 'en';
        }

        return $locale;
    }

    private static function loadTranslations() {
        $locale = self::getLocale();
        $filePath = self::getTranslationsFilePath($locale);

        $translations = file_get_contents($filePath);
        return json_decode($translations, true);
    }

    public static function translate($key) {
        if (!isset(self::$translations)) {
            self::$translations = self::loadTranslations();
        }

        return self::$translations[$key] ?? $key;
    }
}