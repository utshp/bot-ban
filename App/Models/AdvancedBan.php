<?php

namespace App\Models;

use App\Models\Punishment;
use PDO;

class AdvancedBan {
    public static function getPunishments($connection, $type, $active, $search, $page, $perPage) {
        $sql = "SELECT COUNT(*) AS total FROM PunishmentHistory ";

        $conditions = self::getConditions($type, $active, $search);        
    
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
        
        $sql = "SELECT id,
                punishmentType, 
                CASE WHEN punishmentType = 'IP_BAN' OR punishmentType = 'TEMP_IP_BAN' THEN true ELSE false END AS ipban,
                CASE WHEN punishmentType = 'IP_BAN' OR punishmentType = 'TEMP_IP_BAN' THEN NULL ELSE name END AS player_name,
                CASE WHEN operator = '@' OR operator = 'CONSOLE' THEN TRUE ELSE FALSE END AS by_console,
                CASE WHEN operator = '@' OR operator = 'CONSOLE' THEN null ELSE operator END AS admin_name,
                FROM_UNIXTIME(start / 1000) AS start,
                CASE WHEN end = -1 THEN null ELSE FROM_UNIXTIME(end / 1000) END AS end,
                CASE WHEN (SELECT 1 FROM Punishments WHERE Punishments.start = PunishmentHistory.start AND Punishments.uuid = PunishmentHistory.uuid) THEN CASE WHEN end = -1 THEN true WHEN FROM_UNIXTIME(end / 1000) > NOW() THEN true ELSE false END ELSE false END AS active,
                reason
                FROM PunishmentHistory ";
        
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
    
        $punishments = [];
    
        foreach ($data as $punishmentData) {
            $punishments[] = new Punishment(
                $punishmentData['id'],
                self::transformPunishmentType($punishmentData['punishmentType']),
                $punishmentData['ipban'],
                $punishmentData['player_name'],
                null,
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
                null,
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
            'ban' => ['BAN', 'TEMP_BAN', 'IP_BAN', 'TEMP_IP_BAN'],
            'mute' => ['MUTE', 'TEMP_MUTE'],
            'warning' => ['WARNING', 'TEMP_WARNING'],
            'kick' => ['KICK']
        ];
    
        $map = $toPlugin ? array_flip($mapping) : $mapping;
    
        foreach ($map as $output => $actions) {
            if (in_array($input, $actions)) {
                return $output;
            }
        }
    
        return null;
    }

    private static function getPunishmentTypeCondition($type) {
        switch ($type) {
            case 'ban':
                return "(punishmentType = 'BAN' OR punishmentType = 'TEMP_BAN' OR punishmentType = 'IPBAN' OR punishmentType = 'TEMP_IPBAN')";
            case 'mute':
                return "(punishmentType = 'MUTE' OR punishmentType = 'TEMP_MUTE')";
            case 'warning':
                return "(punishmentType = 'WARN' OR punishmentType = 'TEMP_WARNING')";
            case 'kick':
                return "(punishmentType = 'KICK')";
            default:
                return "";
        }
    }

    public static function getPunishmentCount($connection, $type = null) {
        if ($type === null) {
            $sql = "SELECT COUNT(*) AS total FROM PunishmentHistory";
        } else {
            $sql = "SELECT COUNT(*) AS total FROM PunishmentHistory WHERE ";
            $sql .= self::getPunishmentTypeCondition($type);
        }
    
        $statement = $connection->prepare($sql);
    
        $statement->execute();
    
        return $statement->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private static function getConditions($type, $active, $search) {
        $conditions = [];

        if (isset($type)) {
            $conditions[] = self::getPunishmentTypeCondition($type);
        }
        
        if (isset($active)) {
            if ($active) {
                $conditions[] = "CASE WHEN (SELECT 1 FROM Punishments WHERE Punishments.start = PunishmentHistory.start AND Punishments.uuid = PunishmentHistory.uuid) THEN CASE WHEN end = -1 THEN true WHEN FROM_UNIXTIME(end / 1000) > NOW() THEN true ELSE false END ELSE false END = true";
            } else {
                $conditions[] = "CASE WHEN (SELECT 1 FROM Punishments WHERE Punishments.start = PunishmentHistory.start AND Punishments.uuid = PunishmentHistory.uuid) THEN CASE WHEN end = -1 THEN true WHEN FROM_UNIXTIME(end / 1000) > NOW() THEN true ELSE false END ELSE false END = false";
            }
        }       
        
        if (isset($search)) {
            $conditions[] = "punishmentType != 'IP_BAN' AND punishmentType != 'TEMP_IP_BAN'";
            $conditions[] = "name = :search";
        }

        return $conditions;
    }
    
}

?>
