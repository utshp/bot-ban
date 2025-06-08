<?php

namespace App\Models;

use App\Models\Punishment;
use PDO;
use App\Models\Scope;
use App\Config;

class LiteBans {
    public static function getPunishments($connection, $type, $active, $search, $page, $perPage) {
        $prefix = Config::get('CUSTOM_TABLE_PREFIX', 'litebans_');

        if ($type === null) {
            return self::getAllPunishments($connection, $prefix, $active, $search, $page, $perPage);
        } else {
            return self::getPunishmentsByType($connection, $prefix, $type, $active, $search, $page, $perPage);
        }
    }    

    private static function transformPunishmentTypeTable($input, $prefix, $toPlugin = false) {
        $mapping = [
            'ban' => $prefix . 'bans',
            'mute' => $prefix . 'mutes',
            'warning' => $prefix . 'warnings',
            'kick' => $prefix . 'kicks',
        ];

        $map = $toPlugin ? array_flip($mapping) : $mapping;

        foreach ($map as $output => $table) {
            if ($input === $table) {
                return $output;
            }
        }

        return null;
    }

    public static function getPunishmentCount($connection, $type = null) {
        $prefix = Config::get('CUSTOM_TABLE_PREFIX', 'litebans_');

        if ($type === null) {
            $sql = "SELECT (SELECT COUNT(*) FROM " . $prefix . "bans) + (SELECT COUNT(*) FROM " . $prefix . "mutes) + (SELECT COUNT(*) FROM " . $prefix . "warnings) + (SELECT COUNT(*) FROM " . $prefix . "kicks) AS total";
        } else {
            $table = self::transformPunishmentTypeTable($type, $prefix, true);
            $sql = "SELECT COUNT(*) AS total FROM $table";
        }
    
        $statement = $connection->prepare($sql);
    
        $statement->execute();
    
        return $statement->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private static function getPunishmentsByType($connection, $prefix, $type, $active, $search, $page, $perPage) {
        $table = self::transformPunishmentTypeTable($type, $prefix, true);

        $sql = "SELECT COUNT(*) AS total FROM $table ";
    
        $conditions = self::getConditions($table, $prefix, $type, $active, $search);    
    
        if (!empty($conditions)) {
            $sql .= "WHERE " . implode(" AND ", $conditions) . " ";
        }
    
        $statement = $connection->prepare($sql);
        
        if (isset($search)) {
            $statement->bindParam(':search', $search);
        }
    
        $statement->execute();
    
        $totalResults = $statement->fetch(PDO::FETCH_ASSOC)['total'];
    
        $totalPages = ceil($totalResults / $perPage);

        $page = max(1, min(is_numeric($page) ? $page : 1, $totalPages));
    
        $offset = ($page - 1) * $perPage;

        $sql = self::getTableQuery($table, $type, $prefix);
        
        if (!empty($conditions)) {
            $sql .= "WHERE " . implode(" AND ", $conditions) . " ";
        }
    
        $sql .= "ORDER BY ID DESC LIMIT :perPage OFFSET :offset";
    
        $statement = $connection->prepare($sql);

        if (isset($search)) {
            $statement->bindParam(':search', $search);
        }

        $statement->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
    
        $statement->execute();
    
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
    
        $punishments = self::buildPunishments($data);

        $meta = [
            'total_pages' => $totalPages,
            'total_results' => $totalResults,
            'result_count' => count($punishments),
            'page' => $page,
            'offset' => $offset,
        ];
    
        return [
            'punishments' => $punishments,
            'meta' => $meta,
        ];
    }

    private static function getAllPunishments($connection, $prefix, $active, $search, $page, $perPage) {
        $tables = [$prefix . 'bans', $prefix . 'mutes', $prefix . 'warnings', $prefix . 'kicks'];

        $sql = "SELECT ";

        foreach ($tables as $table) {
            $tableCount = "SELECT COUNT(*) FROM $table ";

            $type = self::transformPunishmentTypeTable($table, $prefix);

            $conditions = self::getConditions($table, $prefix, $type, $active, $search);

            if (!empty($conditions)) {
                $tableCount .= "WHERE " . implode(" AND ", $conditions) . " ";
            }

            $tableCounts[] = "($tableCount)";
        }

        $sql .= implode(" + ", $tableCounts) . " AS total ";

        $statement = $connection->prepare($sql);

        if (isset($search)) {
            $statement->bindParam(':search', $search);
        }
    
        $statement->execute();
    
        $totalResults = $statement->fetch(PDO::FETCH_ASSOC)['total'];
    
        $totalPages = ceil($totalResults / $perPage);

        $page = max(1, min(is_numeric($page) ? $page : 1, $totalPages));
    
        $offset = ($page - 1) * $perPage;

        foreach ($tables as $table) {
            $type = self::transformPunishmentTypeTable($table, $prefix);

            $conditions = self::getConditions($table, $prefix, $type, $active, $search);

            $tableQuery = self::getTableQuery($table, $type, $prefix);

            if (!empty($conditions)) {
                $tableQuery .= "WHERE " . implode(" AND ", $conditions) . " ";
            }

            $tableQuerys[] = $tableQuery;

        }

        $sql = implode(" UNION ALL ", $tableQuerys) . " ";

        $sql .= "ORDER BY start DESC LIMIT :perPage OFFSET :offset";

        $statement = $connection->prepare($sql);

        if (isset($search)) {
            $statement->bindParam(':search', $search);
        }

        $statement->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);

        $statement->execute();
    
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
    
        $punishments = self::buildPunishments($data);

        $meta = [
            'total_pages' => $totalPages,
            'total_results' => $totalResults,
            'result_count' => count($punishments),
            'page' => $page,
            'offset' => $offset,
        ];
    
        return [
            'punishments' => $punishments,
            'meta' => $meta,
        ];
    }

    private static function getConditions($table, $prefix, $type, $active, $search) {
        $conditions = [];
        
        if (isset($active)) {
            if ($active) {
                $conditions[] = "CASE WHEN '$type' = 'kick' THEN false ELSE CASE WHEN active = 1 THEN CASE WHEN until = 0 THEN true WHEN FROM_UNIXTIME(until / 1000) > NOW() THEN true ELSE false END ELSE false END END = true";
            } else {
                $conditions[] = "CASE WHEN '$type' = 'kick' THEN false ELSE CASE WHEN active = 1 THEN CASE WHEN until = 0 THEN true WHEN FROM_UNIXTIME(until / 1000) > NOW() THEN true ELSE false END ELSE false END END = false";
            }
        }
        
        if (isset($search)) {
            $conditions[] = "EXISTS (SELECT 1 FROM " . $prefix . "history WHERE " . $prefix . "history.uuid = $table.uuid AND " . $prefix . "history.name = :search)";
        }

        return $conditions;
    }

    private static function getCancelledLogic($type) {
        if ($type !== 'kick') {
            $cancelledLogic = "CASE WHEN removed_by_uuid IS NULL THEN false ELSE true END AS cancelled,
                               CASE WHEN removed_by_uuid IS NULL THEN null ELSE CASE WHEN removed_by_uuid = 'CONSOLE' OR removed_by_uuid = '@' THEN true else false END END AS cancelled_by_console,
                               CASE WHEN removed_by_uuid IS NULL THEN null ELSE CASE WHEN removed_by_uuid = 'CONSOLE' OR removed_by_uuid = '@' THEN null else removed_by_name END END AS cancelled_by_name,
                               CASE WHEN removed_by_uuid IS NULL THEN null ELSE removed_by_date END AS cancelled_by_date,
                               CASE WHEN removed_by_uuid IS NULL THEN null ELSE removed_by_reason END AS cancelled_by_reason";
        } else {
            $cancelledLogic = "null AS cancelled,
                               null AS cancelled_by_console,
                               null AS cancelled_by_name,
                               null AS cancelled_by_date,
                               null AS cancelled_by_reason";
        }

        return $cancelledLogic;
    }

    private static function getTableQuery($table, $type, $prefix) {
        $cancelledLogic = self::getCancelledLogic($type);

        $tableQuery = "SELECT id,
                        CASE WHEN ipban = 1 THEN true ELSE false END AS ipban,
                        CASE WHEN ipban = 1 THEN null ELSE (SELECT name FROM " . $prefix . "history WHERE " . $prefix . "history.uuid = $table.uuid LIMIT 1) END AS player_name,
                        CASE WHEN banned_by_uuid = 'CONSOLE' OR banned_by_uuid = '@' THEN true ELSE false END AS by_console,
                        CASE WHEN banned_by_uuid = 'CONSOLE' OR banned_by_uuid = '@' THEN null ELSE banned_by_name END AS admin_name,
                        FROM_UNIXTIME(time / 1000) AS start,
                        CASE WHEN until = 0 THEN null ELSE FROM_UNIXTIME(until / 1000) END AS end,
                        CASE WHEN '$type' = 'kick' THEN false ELSE CASE WHEN active = 1 THEN CASE WHEN until = 0 THEN true WHEN FROM_UNIXTIME(until / 1000) > NOW() THEN true ELSE false END ELSE false END END AS active,
                        $cancelledLogic,
                        reason,
                        server_origin,
                        '$type' AS type,
                        CASE WHEN server_scope = '*' THEN null ELSE server_scope END AS scope
                        FROM $table ";

        return $tableQuery;
    }

    private static function buildPunishments($data) {
        $punishments = [];

        foreach ($data as $punishmentData) {
            $punishments[] = new Punishment(
                $punishmentData['id'],
                $punishmentData['type'],
                $punishmentData['ipban'],
                $punishmentData['player_name'],
                null,
                $punishmentData['by_console'],
                $punishmentData['admin_name'],
                $punishmentData['start'],
                $punishmentData['end'],
                $punishmentData['active'],
                $punishmentData['cancelled'],
                $punishmentData['cancelled_by_console'],
                $punishmentData['cancelled_by_name'],
                $punishmentData['cancelled_by_date'],
                $punishmentData['cancelled_by_reason'],
                $punishmentData['scope'] ? new Scope($punishmentData['scope']) : null,
                $punishmentData['server_origin'] ? new Scope($punishmentData['server_origin']) : null,
                $punishmentData['reason'],
            );
        }

        return $punishments;
    }
    
}

?>
