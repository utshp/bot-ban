<?php

namespace App\Models;

use App\Models\Punishment;
use PDO;
use App\Models\Scope;

class LibertyBans {
    public static function getPunishments($connection, $type, $active, $search, $page, $perPage) {
        $sql = "SELECT COUNT(*) AS total FROM libertybans_simple_history ";

        $searchUuid = Punishment::getUuid($search);
    
        $conditions = self::getConditions($type, $active, $search, $searchUuid);
    
        if (!empty($conditions)) {
            $sql .= "WHERE " . implode(" AND ", $conditions) . " ";
        }
    
        $statement = $connection->prepare($sql);
    
        self::bindConditionParams($statement, $type, $search, $searchUuid);
    
        $statement->execute();
    
        $totalResults = $statement->fetch(PDO::FETCH_ASSOC)['total'];
    
        $totalPages = ceil($totalResults / $perPage);

        $page = max(1, min(is_numeric($page) ? $page : 1, $totalPages));
    
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT id,
                type, 
                CASE WHEN victim_type != 0 THEN true ELSE false END AS ipban,
                CASE WHEN victim_type != 0 THEN null ELSE (SELECT name FROM libertybans_latest_names WHERE libertybans_latest_names.uuid = libertybans_simple_history.victim_uuid LIMIT 1) END AS player_name,
                CASE WHEN operator = 0x00000000000000000000000000000000 THEN true ELSE false END AS by_console,
                CASE WHEN operator = 0x00000000000000000000000000000000 THEN null ELSE (SELECT name FROM libertybans_latest_names WHERE libertybans_latest_names.uuid = libertybans_simple_history.operator LIMIT 1) END AS admin_name,
                start,
                CASE WHEN end = 0 THEN null ELSE end END AS end,
                CASE WHEN (SELECT 1 FROM libertybans_simple_active WHERE libertybans_simple_active.id = libertybans_simple_history.id) THEN CASE WHEN end = 0 THEN true WHEN end > UNIX_TIMESTAMP(NOW()) THEN true ELSE false END ELSE false END AS active,
                reason,
                HEX(victim_uuid) AS player_uuid,
                CASE WHEN scope_type = 1 OR scope_type = 2 THEN scope ELSE null END AS scope
                FROM libertybans_simple_history ";
        
        if (!empty($conditions)) {
            $sql .= "WHERE " . implode(" AND ", $conditions) . " ";
        }
    
        $sql .= "ORDER BY ID DESC LIMIT :perPage OFFSET :offset";
    
        $statement = $connection->prepare($sql);
    
        self::bindConditionParams($statement, $type, $search, $searchUuid);

        $statement->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
    
        $statement->execute();
    
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
    
        $punishments = [];
    
        foreach ($data as $punishmentData) {
            $punishments[] = new Punishment(
                $punishmentData['id'],
                self::transformPunishmentType($punishmentData['type']),
                $punishmentData['ipban'],
                $punishmentData['player_name'],
                $punishmentData['player_uuid'],
                $punishmentData['by_console'],
                $punishmentData['admin_name'],
                $punishmentData['start'],
                $punishmentData['end'],
                $punishmentData['active'],
                null,
                null,
                null,
                null,
                null,
                $punishmentData['scope'] ? new Scope($punishmentData['scope']) : null,
                null,
                $punishmentData['reason'],
            );
        }

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

    private static function transformPunishmentType($input, $toPlugin = false) {
        $mapping = [
            'ban' => 0,
            'mute' => 1,
            'warning' => 2,
            'kick' => 3
        ];
    
        $map = $toPlugin ? $mapping : array_flip($mapping);
    
        return $map[$input] ?? null;
    }

    public static function getPunishmentCount($connection, $type = null) {
        if ($type === null) {
            $sql = "SELECT COUNT(*) AS total FROM libertybans_simple_history";
        } else {
            $typeNumber = self::transformPunishmentType($type, true);
            $sql = "SELECT COUNT(*) AS total FROM libertybans_simple_history WHERE type = :type";
        }
    
        $statement = $connection->prepare($sql);
        
        if ($type !== null) {
            $statement->bindParam(':type', $typeNumber);
        }
    
        $statement->execute();
    
        return $statement->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private static function getConditions($type, $active, $search, $searchUuid) {
        $conditions = [];

        if (isset($type)) {
            $type = self::transformPunishmentType($type, true);
            $conditions[] = "type = :type";
        }
        
        if (isset($active)) {
            if ($active) {
                $conditions[] = "CASE WHEN (SELECT 1 FROM libertybans_simple_active WHERE libertybans_simple_active.id = libertybans_simple_history.id) THEN CASE WHEN end = 0 THEN true WHEN end > UNIX_TIMESTAMP(NOW()) THEN true ELSE false END ELSE false END = true";
            } else {
                $conditions[] = "CASE WHEN (SELECT 1 FROM libertybans_simple_active WHERE libertybans_simple_active.id = libertybans_simple_history.id) THEN CASE WHEN end = 0 THEN true WHEN end > UNIX_TIMESTAMP(NOW()) THEN true ELSE false END ELSE false END = false";
            }
        }
        
        if (isset($search)) {
            $conditions[] = "victim_type = 0";

            $searchUuidCondition = isset($searchUuid) ? "OR HEX(victim_uuid) = :searchUuid" : "";

            $conditions[] = "(EXISTS (SELECT 1 FROM libertybans_latest_names WHERE libertybans_latest_names.uuid = libertybans_simple_history.victim_uuid AND libertybans_latest_names.name = :search) $searchUuidCondition)";
        }
        
        return $conditions;
    }

    private static function bindConditionParams($statement, $type, $search, $searchUuid) {
        if (isset($type)) {
            $type = self::transformPunishmentType($type, true);
            $statement->bindParam(':type', $type);
        }
        
        if (isset($search)) {
            $statement->bindParam(':search', $search);
        }

        if (isset($searchUuid)) {
            $statement->bindParam(':searchUuid', $searchUuid);
        }

        return $statement;
    }
    
}

?>
