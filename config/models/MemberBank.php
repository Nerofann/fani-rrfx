<?php
namespace App\Models;

use Config\Core\Database;
use Config\Core\SystemInfo;
use Exception;

class MemberBank {

    public static $statusNotVerified = 0;    
    public static $statusVerified = -1;    
    public static $statusAccepted = 1;    
    public static $statusRejected = 2;  
    
    public static function status(int $status) {
        switch($status) {
            case self::$statusNotVerified: 
                return [
                    'text' => "Not Verified",
                    'html' => '<span class="badge bg-warning">Not Verified</span>'
                ];

            case self::$statusVerified: 
                return [
                    'text' => "Verified",
                    'html' => '<span class="badge bg-info">Verified</span>'
                ];

            case self::$statusAccepted: 
                return [
                    'text' => "Aktif",
                    'html' => '<span class="badge bg-success">Aktif</span>'
                ];

            case self::$statusRejected: 
                return [
                    'text' => "Ditolak",
                    'html' => '<span class="badge bg-danger">Ditolak</span>'
                ];
        }
    }

    public static function findByIdHash(string $idHash): array|bool {
        try {
            $db = Database::connect();
            $sqlGet = $db->query("SELECT * FROM tb_member_bank WHERE MD5(MD5(ID_MBANK)) = '{$idHash}' LIMIT 1");
            if($sqlGet->num_rows == 0) {
                return false;
            }

            return $sqlGet->fetch_assoc() ?? false;

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    }
}
