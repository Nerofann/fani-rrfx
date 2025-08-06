<?php 
namespace App\Models;

use Config\Core\Database;
use Config\Core\SystemInfo;
use Exception;

class Dpwd {

    public static array $status = [
        "-1" => [
            'html' => '<span class="badge bg-success">Open</span>',
            'text' => "Open"
        ],
        "1" => [
            'html' => '<span class="badge bg-danger">Closed</span>',
            'text' => "Closed"
        ]
    ];

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

    public static function findById(string $id): array|bool {
        try {
            $db = Database::connect();
            $sqlGet = $db->query("SELECT * FROM tb_dpwd JOIN tb_racc ON(tb_dpwd.DPWD_RACC = tb_racc.ID_ACC) WHERE MD5(MD5(tb_dpwd.ID_DPWD)) = '{$id}' LIMIT 1");
            return $sqlGet->fetch_assoc() ?? false;

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    }

}