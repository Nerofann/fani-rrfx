<?php
namespace App\Models;

use Config\Core\Database;
use Config\Core\TokenGenerator;
use Exception;

class Token extends TokenGenerator {
    public function __construct() {

    }

    public static function saveTokens(int $userId, $accessToken, $refreshToken): array|bool {
        try {
            global $db;
            if(!$db) {
                return false;
            }
    
            $accessTokenExpires = date('Y-m-d H:i:s', time() + ACCESS_TOKEN_LIFETIME);
            $refreshTokenExpires = date('Y-m-d H:i:s', time() + REFRESH_TOKEN_LIFETIME);
    
            return Database::insert("tb_member_token", [
                'mbr_id' => $userId,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'access_token_expires' => $accessTokenExpires,
                'refresh_token_expires' => $refreshTokenExpires,
            ]);

        } catch (Exception $e) {
            if(ini_get("display_errors") == "1") {
                throw $e;
            }

            return false;
        }
    }

    public static function findValidRefreshToken($token): bool|array {
        global $db;
        if(!$db) {
            return false;
        }

        $sqlGet = $db->query("SELECT * FROM tb_member_token WHERE refresh_token = '$token' AND refresh_token_expires > NOW() AND is_revoked = 0");
        return $sqlGet->fetch_assoc() ?? false;
    }

    public static function revokeToken(string $token): bool {
        global $db;
        if(!$db) {
            return false;
        }

        $sqlUpdate = $db->prepare("DELETE FROM tb_member_token WHERE refresh_token = '$token'");
        return $sqlUpdate->execute();
    }
}