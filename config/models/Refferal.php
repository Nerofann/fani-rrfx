<?php
namespace App\Models;

use Config\Core\Database;
use Config\Core\SystemInfo;
use Exception;

class Refferal {

    public static $endpoint = "http://client-rrfx.test/referral";


    public static function createUserReferral(int $mbrid): string|bool {
        try {
            $db = Database::connect();
            $sqlGet = $db->query("SELECT * FROM tb_member WHERE MBR_ID = {$mbrid} LIMIT 1");
            if($sqlGet->num_rows != 1) {
                return false;
            }

            $userData = $sqlGet->fetch_assoc();
            return self::$endpoint."/".($userData['MBR_CODE'] ?? "-");

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    }

    public static function createUserGroupReferral(int $mbrid): array {
        try {
            $db = Database::connect();
            $sqlGet = $db->query("
                SELECT 
                    tm.MBR_ID,
                    tm.MBR_EMAIL,
                    tm.MBR_NAME,
                    tr.ID_ACC,
                    tr.ACC_LOGIN,
                    tr.ACC_KODE,
                    trt.RTYPE_NAME,
                    trt.RTYPE_TYPE,
                    trt.RTYPE_RATE,
                    trt.RTYPE_KOMISI
                FROM tb_member tm
                JOIN tb_racc tr ON (tr.ACC_MBR = tm.MBR_ID)
                JOIN tb_racctype trt ON (trt.ID_RTYPE = ACC_TYPE)
                WHERE MBR_ID = {$mbrid}
                AND tr.ACC_DERE = 1
                AND tr.ACC_WPCHECK = 6
                AND tr.ACC_STS = -1
                GROUP BY trt.RTYPE_TYPE
            ");

            /** Get Account */
            $result = [];
            $accounts = $sqlGet->fetch_all(MYSQLI_ASSOC);
            if(empty($accounts)) {
                return [];
            }

            foreach($accounts as $acc) {
                $result[] = [
                    'type' => $acc['RTYPE_TYPE'],
                    'link' => self::$endpoint . "/" . implode("-", [strtolower($acc['RTYPE_TYPE']), hash("sha256", $acc['ACC_KODE'])])
                ];
            }

            return $result;

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function createAccountReferral(int $mbrid): array {
        try {
            $result = [];
            $accounts = Account::myAccount($mbrid);
            foreach($accounts as $acc) {
                $result[] = [
                    'name' => $acc['RTYPE_NAME'],
                    'type' => $acc['RTYPE_TYPE'],
                    'login' => $acc['ACC_LOGIN'],
                    'rate' => ($acc['RTYPE_ISFLOATING'])? "Floating" : $acc['RTYPE_RATE'],
                    'commission' => $acc['RTYPE_KOMISI'], 
                    'link' => self::$endpoint . "/" . implode("-", [$acc['RTYPE_SUFFIX'], strtolower($acc['RTYPE_TYPE']), $acc['ACC_KODE']])
                ];
            }

            return $result;

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    } 

}