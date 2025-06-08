<?php

namespace App\Models;

use App\Config;

class Scope {
    public $value;
    public $name;
    public $color;

    public function __construct($value) {
        $this->value = $value;
        $this->name = $this->getName($value);
        $this->color = $this->getColor($value);
    }

    private function getName($value) {
        $customScopeNames = Config::get('CUSTOM_SCOPE_NAMES', []);

        return $customScopeNames[$value] ?? null;
    }

    private function getColor($value) {
        $customScopeColors = Config::get('CUSTOM_SCOPE_COLORS', []);

        return $customScopeColors[$value] ?? Config::get('DEFAULT_SCOPE_COLOR', 'blue');
    }
}

?>
