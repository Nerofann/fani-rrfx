<?php 
namespace App\Models;

use Config\Core\Database;
use Config\Core\SystemInfo;
use Exception;

class Dpwd {

    public static array $status = [
        "-1" => [
            'html' => '<span class="badge bg-success">Berhasil</span>',
            'text' => "Berhasil"
        ],
        "0" => [
            'html' => '<span class="badge bg-warning">Pending</span>',
            'text' => "Pending"
        ],
        "1" => [
            'html' => '<span class="badge bg-danger">Ditolak</span>',
            'text' => "Ditolak"
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
            $sqlGet = $db->query("SELECT * FROM tb_dpwd WHERE MD5(MD5(tb_dpwd.ID_DPWD)) = '{$id}' LIMIT 1");
            return $sqlGet->fetch_assoc() ?? false;

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    }

}