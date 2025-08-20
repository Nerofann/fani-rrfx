<?php
namespace App\Models;

use Config\Core\Database;
use Config\Core\SystemInfo;
use Exception;

class Ib {

    public static array $userType = [
        '1' => "Mib",
        '2' => "Ib",
        '3' => "Trader"
    ];

    public static array $status = [
        '-1' => [
            'text' => "Success",
            'html' => '<span class="badge bg-success">Success</span>'
        ],
        '0' => [
            'text' => "Pending",
            'html' => '<span class="badge bg-warning">Pending</span>'
        ],
        '1' => [
            'text' => "Rejected",
            'html' => '<span class="badge bg-danger">Rejected</span>'
        ],
    ];

    public static array $requiredAccounts = ['STANDARD', 'STANDARD-PLUS'];

    public static function getIbType(): bool|int {
        return array_search("Ib", self::$userType);
    }

    public static function getTraderType(): bool|int {
        return array_search("Trader", self::$userType);
    }

    public static function isAllowToBecomeIb(string $mbridHash): array {
        try {
            /** 
             * Persyaratan
             * - Memiliki Standard Account
             * - Memiliki setidaknya $100 margin free di salah satu account (bukan summary)
             * 
            *  */
            $result = [
                'success' => false,
                'requirements' => [
                    'haveRequiredAccount' => [
                        'status' => false,
                        'text' => "Have a standard account",
                    ],
                    'haveEnoughBalance' => [
                        'status' => false,
                        'text' => "Have at least $100 free margin in real accounts"
                    ]
                ]
            ];

            $isHaveRequiredAccount = self::haveRequiredAccount($mbridHash);
            if(is_array($isHaveRequiredAccount)) {
                $accounts = $isHaveRequiredAccount; 
                $result['requirements']['haveRequiredAccount']['status'] = true;

                foreach($accounts as $sac) {
                    if($sac['MARGIN_FREE'] >= 100) {
                        $result['requirements']['haveEnoughBalance']['status'] = true;
                        $result['success'] = true;
                        break;
                    }
                }
            }


            return $result;

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return $result;
        }
    }

    public static function haveRequiredAccount(string $userid): array|bool {
        try {
            global $db;
            $sqlGet = $db->query("
                SELECT 
                    ID_ACC, 
                    ACC_LOGIN, 
                    RTYPE_CURR, 
                    ACC_TYPE, 
                    RTYPE_TYPE,
                    ACC_PASS,
                    ACC_INVESTOR,
                    mt5u.BALANCE,
                    mt5u.MARGIN_FREE
                FROM tb_racc 
                JOIN tb_racctype ON (ID_RTYPE = ACC_TYPE)
                JOIN mt5_users mt5u ON (mt5u.LOGIN = ACC_LOGIN)
                WHERE MD5(MD5(ACC_MBR)) = '{$userid}'
                AND ACC_DERE = 1 
                AND ACC_LOGIN != 0
                AND ACC_WPCHECK = 6
                AND FIND_IN_SET(UPPER(RTYPE_TYPE), '".implode(",", self::$requiredAccounts)."') 
            ");

            if($sqlGet->num_rows == 0) {
                return false;
            }

            return $sqlGet->fetch_all(MYSQLI_ASSOC);

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
            $sqlGet = $db->query("SELECT * FROM tb_become_ib WHERE MD5(MD5(ID_BECOME)) = '{$id}' LIMIT 1");
            if($sqlGet->num_rows != 1) {
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