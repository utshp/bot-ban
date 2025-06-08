<?php

namespace App\Controllers;

class TypeController {
    public static function getTitle($type) {
      switch ($type) {
          case 'ban':
              return LanguageController::translate('Bans');
          case 'kick':
              return LanguageController::translate('Kicks');
          case 'mute':
              return LanguageController::translate('Mutes');
          case 'warning':
              return LanguageController::translate('Warnings');
          default:
              return LanguageController::translate('Banlist');
      }
    }

    public static function transformType($input, $reverse = false) {
        $mapping = [
            'bans' => 'ban',
            'kicks' => 'kick',
            'mutes' => 'mute',
            'warnings' => 'warning'
        ];

        $map = $reverse ? array_flip($mapping) : $mapping;
    
        return $map[$input] ?? null;
    }    

    public static function getLabel($type) {
        switch ($type) {
            case 'ban':
                return LanguageController::translate('Ban');
            case 'kick':
                return LanguageController::translate('Kick');
            case 'mute':
                return LanguageController::translate('Mute');
            case 'warning':
                return LanguageController::translate('Warning');
            default:
                return '';
        }
    }
}
