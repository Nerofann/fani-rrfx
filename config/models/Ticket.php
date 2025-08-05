<?php 
namespace App\Models;

use Config\Core\Database;
use Config\Core\SystemInfo;
use Exception;

class Ticket {

    public static function findByCode(string $code): array|bool {
        try {
            $db = Database::connect();
            $sqlGet = $db->query("SELECT * FROM tb_ticket WHERE TICKET_CODE = '{$code}' LIMIT 1");
            return $sqlGet->fetch_assoc() ?? false;

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    }

    public static function historyChatByTicketCode(string $code, int $maxDayHistory = 3): array {
        try {
            $dateMax = date("Y-m-d", strtotime("-{$maxDayHistory} day"));
            $db = Database::connect();
            $sqlGet = $db->query("SELECT * FROM tb_ticket_detail WHERE TDETAIL_TCODE = '{$code}' AND DATE(TDETAIL_DATETIME) >= '{$dateMax}'  ORDER BY TDETAIL_DATETIME");
            return $sqlGet->fetch_all(MYSQLI_ASSOC) ?? [];

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

}