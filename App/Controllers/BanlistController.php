<?php

namespace App\Controllers;

use App\Database;
use App\Models\LibertyBans;
use App\Models\AdvancedBan;
use App\Models\LiteBans;
use PDOException;
use App\Config;

class BanlistController
{
    public static function index($type)
    {
        $connectionError = null;
        $databaseError = null;

        try {
            $database = new Database();
            $connection = $database->connect(false);
        } catch (PDOException $e) {
            $connectionError = $e->getMessage();
        }


        $perPage = 15;


        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        $status = self::getStatus(isset($_GET['status']) ? $_GET['status'] : null);

        $search = isset($_GET['search']) ? $_GET['search'] : null;


        $punishments = [];

        $punishmentCount = 0;
        $banCount = 0;
        $muteCount = 0;
        $warningCount = 0;
        $kickCount = 0;

        if (!isset($connectionError)) {
            $pluginModel = self::getPluginModel();

            try {
                $punishmentCount = $pluginModel->getPunishmentCount($connection);
                $banCount = $pluginModel->getPunishmentCount($connection, 'ban');
                $muteCount = $pluginModel->getPunishmentCount($connection, 'mute');
                $warningCount = $pluginModel->getPunishmentCount($connection, 'warning');
                $kickCount = $pluginModel->getPunishmentCount($connection, 'kick');

                $data = $pluginModel->getPunishments($connection, $type, self::transformStatus($status), self::transformSearch($search), $page, $perPage);

                $punishments = $data['punishments'];
                $meta = $data['meta'];
            } catch (PDOException $e) {
                $databaseError = $e->getMessage();
            }
        }

        $connection = null;
        
        require __DIR__ . '/../../views/banlist.php';
    }

    private static function getStatus($status) {
        $allowedStatuses = ['all', 'active', 'inactive'];
        return in_array($status, $allowedStatuses) ? $status : null;
    }

    private static function transformStatus($status) {
        switch ($status) {
            case 'active':
                return true;
            case 'inactive':
                return false;
            default:
                return null;
        }
    }

    private static function transformSearch($search) {
        if ($search === '') {
            return null;
        } else {
            return $search;
        }
    }

    private static function getPluginModel() {
        if (Config::get('PUNISHMENT_PLUGIN') === 'litebans') {
            $pluginModel = new LiteBans();
        } elseif (Config::get('PUNISHMENT_PLUGIN') === 'advancedban') {
            $pluginModel = new AdvancedBan();
        } else {
            $pluginModel = new LibertyBans();
        }

        return $pluginModel;
    }
}
