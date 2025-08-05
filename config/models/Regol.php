<?php
namespace App\Models;

use Config\Core\Database;
use Config\Core\SystemInfo;
use Exception;

class Regol {

    public static function generatePassword(int $len = 8) {
        $lower = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
        $upper = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $specials = array('!','#','$','%','&','(',')','*','+',',','-','.',':',';','=','?','@','[',']','^','_','{','|','}','~');
        $digits = array('0','1','2','3','4','5','6','7','8','9');
        $all = array($lower, $upper, $specials, $digits);

        $pwd = $lower[array_rand($lower, 1)];
        $pwd = $pwd . $upper[array_rand($upper, 1)];
        $pwd = $pwd . $specials[array_rand($specials, 1)];
        $pwd = $pwd . $digits[array_rand($digits, 1)];

        for($i = strlen($pwd); $i < max(8, $len); $i++)
        {
            $temp = $all[array_rand($all, 1)];
            $pwd = $pwd . $temp[array_rand($temp, 1)];
        }

        return str_shuffle($pwd);
    } 

    public static function getLastAccount(string $userid): array|bool {
        try {
            $db = Database::connect();
            $sqlGet = $db->query("
                SELECT 
                    tr.*,
                    tra.*
                FROM tb_racc tr 
                JOIN tb_racctype tra ON (tra.ID_RTYPE = tr.ACC_TYPE) 
                WHERE UPPER(tra.RTYPE_TYPE) != 'DEMO'
                AND tr.ACC_LOGIN != 0
                AND tr.ACC_WPCHECK = 6
                AND MD5(MD5(tr.ACC_MBR)) = '{$userid}'
                ORDER BY ID_ACC DESC 
                LIMIT 1
            ");

            if($sqlGet->num_rows == 0) {
                return false;
            }

            return $sqlGet->fetch_assoc();

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    }

    public static function duplicateLastAccount(string $userid) {
        try {
            /** Get Last Account */
            $lastAccount = self::getLastAccount($userid);
            unset(
                $lastAccount['ID_ACC'], 
                $lastAccount['ACC_WPCHECK_DATE'], 
                $lastAccount['ACC_PASS'], 
                $lastAccount['ACC_INVESTOR'], 
                $lastAccount['ACC_PASS'], 
                $lastAccount['ACC_PASSPHONE'], 
                $lastAccount['ACC_INITIALMARGIN'],
                $lastAccount['ACC_LAST_STEP'],
                $lastAccount['ACC_F_CMPLT'],
                $lastAccount['ACC_F_CMPLT_IP'],
                $lastAccount['ACC_F_CMPLT_PERYT'],
                $lastAccount['ACC_F_CMPLT_DATE'],
                $lastAccount['ACC_KODE_NASABAH'],
            );

            $lastAccount['ACC_DATETIME'] = date("Y-m-d H:i:s");
            $lastAccount['ACC_STS'] = 0; 
            $lastAccount['ACC_LOGIN'] = 0;
            $lastAccount['ACC_WPCHECK'] = 0;

            /** Insert New Record */
            return Database::insert("tb_racc", $lastAccount);

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    } 

    public static function isAllowToEdit(int $status): bool {
        if(in_array($status, [1, -1])) {
            JsonResponse([
                'success' => false,
                'message' => "Akun sedang dalam prosess verifikasi",
                'data' => []
            ]);
        }

        return true;
    }

    public static function createDemo() {
        
    }
}