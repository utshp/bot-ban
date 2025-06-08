<?php

namespace App\Models;

class Punishment {
    public $id;
    public $type;
    public $ipban;
    public $player_name;
    public $by_console;
    public $admin_name;
    public $start;
    public $end;
    public $active;
    public $cancelled;
    public $cancelled_by_console;
    public $cancelled_by_name;
    public $cancelled_by_date;
    public $cancelled_by_reason;
    public $scope;
    public $server_origin;
    public $reason;

    public function __construct($id, $type, $ipban, $player_name, $uuid, $by_console, $admin_name, $start, $end, $active, $cancelled, $cancelled_by_console, $cancelled_by_name, $cancelled_by_date, $cancelled_by_reason, $scope, $server_origin, $reason) {

        if (!$ipban && !isset($player_name) && isset($uuid)) {
            $player_name = self::getName($uuid);
        }

        $this->id = $id;
        $this->type = $type;
        $this->ipban = $ipban;
        $this->player_name = $player_name;
        $this->by_console = $by_console;
        $this->admin_name = $admin_name;
        $this->start = $start;
        $this->end = $end;
        $this->active = $active;
        $this->cancelled = $cancelled;
        $this->cancelled_by_console = $cancelled_by_console;
        $this->cancelled_by_name = $cancelled_by_name;
        $this->cancelled_by_date = $cancelled_by_date;
        $this->cancelled_by_reason = $cancelled_by_reason;
        $this->scope = $scope;
        $this->server_origin = $server_origin;
        $this->reason = $reason;
    }

    private function getName($uuid) {
        $api_url = 'https://sessionserver.mojang.com/session/minecraft/profile/' . $uuid;
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        
        if ($response === false) {
            curl_close($ch);
            return null;
        }
        
        curl_close($ch);
        
        $data = json_decode($response, true);
        
        if (isset($data['name'])) {
            return $data['name'];
        } else {
            return null;
        }
    }

    public static function getUuid($name) {
        $api_url = 'https://api.mojang.com/users/profiles/minecraft/' . $name;
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        
        if ($response === false) {
            curl_close($ch);
            return null;
        }
        
        curl_close($ch);
        
        $data = json_decode($response, true);
        
        if (isset($data['id'])) {
            return $data['id'];
        } else {
            return null;
        }
    }

}

?>
