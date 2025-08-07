<?php
namespace App\Models;

use App\Models\Helper;
use Config\Core\Database;
use Config\Core\SystemInfo;
use Exception;

class Account {
    public static $currency = ['USD', 'USC'];
    public static $product_link_pdf = [
        'lite' => "https://cabinet-tridentprofutures.techcrm.net/assets/trading-rules/lite.pdf",
        'standard' => "https://cabinet-tridentprofutures.techcrm.net/assets/trading-rules/standard.pdf",
        'pro' => "https://cabinet-tridentprofutures.techcrm.net/assets/trading-rules/pro.pdf",
        'micro' => "https://cabinet-tridentprofutures.techcrm.net/assets/trading-rules/micro.pdf",
        '10001' => "https://cabinet-tridentprofutures.techcrm.net/assets/trading-rules/10001.pdf",
        '10002' => "https://cabinet-tridentprofutures.techcrm.net/assets/trading-rules/10002.pdf",
        '20001' => "https://cabinet-tridentprofutures.techcrm.net/assets/trading-rules/20001.pdf",
        '20002' => "https://cabinet-tridentprofutures.techcrm.net/assets/trading-rules/20002.pdf",
    ];

    public static $tipeIdentitas = ['KTP', 'PASSPORT'];

    public function __construct() {
        
    }

    public static function realAccountDetail(string $idAcc) {
        try {
            global $db;
            $sqlGet = $db->query("
                SELECT 
                    tr.*,
                    tra.*,
                    tm.*,
                    (
                        SELECT
                            JSON_ARRAYAGG(
                                JSON_OBJECT(
                                    'MBANK_NAME', tb_member_bank.MBANK_NAME,
                                    'MBANK_HOLDER', tb_member_bank.MBANK_HOLDER,
                                    'MBANK_ACCOUNT', tb_member_bank.MBANK_ACCOUNT
                                )
                            )
                        FROM tb_member_bank
                        WHERE tb_member_bank.MBANK_MBR = tm.MBR_ID
                        LIMIT 1
                    ) AS MBR_BKJSN
                FROM tb_racc tr 
                JOIN tb_member tm ON (tm.MBR_ID = tr.ACC_MBR)
                JOIN tb_racctype tra ON (tra.ID_RTYPE = tr.ACC_TYPE)
                WHERE UPPER(tra.RTYPE_TYPE) != 'DEMO'
                AND MD5(MD5(tr.ID_ACC)) = '{$idAcc}'
                LIMIT 1
            ");

            return $sqlGet->fetch_assoc() ?? [];

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function realAccountDetail_byLogin(string $login) {
        try {
            global $db;
            $sqlGet = $db->query("
                SELECT 
                    tr.*,
                    tra.*,
                    tm.*
                FROM tb_racc tr 
                JOIN tb_member tm ON (tm.MBR_ID = tr.ACC_MBR)
                JOIN tb_racctype tra ON (tra.ID_RTYPE = tr.ACC_TYPE)
                WHERE UPPER(tra.RTYPE_TYPE) != 'DEMO'
                AND tr.ACC_LOGIN = '{$login}'
                LIMIT 1
            ");

            return $sqlGet->fetch_assoc() ?? [];

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function accoundCondition(int $idAcc) {
        try {
            global $db;
            $sqlGet = $db->query("
                SELECT 
                    tac.*,
                    tm.MBR_NAME,
	                tm.MBR_EMAIL
                FROM `tb_acccond` tac
                JOIN tb_racc tr ON (tr.ID_ACC = tac.ACCCND_ACC)
                LEFT JOIN tb_member tm ON (tm.MBR_ID = tac.ACCCND_IB)
                WHERE tr.ID_ACC = {$idAcc}
                ORDER BY ID_ACCCND DESC
                LIMIT 1
            ");

            return $sqlGet->fetch_assoc() ?? [];

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function marginBalance(int $accLogin) {
        // try {
        //     global $db;
        //     $sqlGetAccount = $db->query("SELECT (MARGIN_FREE-CREDIT) AS BALANCE, CREDIT FROM MT4_USERS WHERE `LOGIN` = {$accLogin} LIMIT 1");
        //     if($sqlGetAccount->num_rows != 1) {
        //         return "Invalid Account";
        //     }

        //     $assoc = $sqlGetAccount->fetch_assoc();
        //     return floatval($assoc['BALANCE'] ?? 0) ;

        // } catch (Exception $e) {
        //     if(SystemInfo::isDevelopment()) {
        //         return $e->getMessage();
        //     }

        //     return "Invalid";
        // }
        return 1000000;
    }

    public static function creditBalance(int $accLogin) {
        try {
            global $db;
            $sqlGetAccount = $db->query("SELECT CREDIT FROM MT4_USERS WHERE `LOGIN` = {$accLogin} LIMIT 1");
            if($sqlGetAccount->num_rows != 1) {
                return "Invalid Account";
            }

            $assoc = $sqlGetAccount->fetch_assoc();
            return floatval($assoc['CREDIT'] ?? 0) ;

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                return $e->getMessage();
            }

            return "Invalid";
        }
    }

    public static function checkMetaDpwd(string $code) {
        try {
            global $db;
            $sqlGet = $db->query("SELECT TICKET FROM MT4_TRADES WHERE COMMENT = '{$code}' LIMIT 1");
            if($sqlGet->num_rows != 1) {
                return [];
            }

            return $sqlGet->fetch_assoc() ?? [];

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function accountConvertation(array $data = []): array|string {
        try {
            /** Required parameter */
            foreach(['account_id', 'amount', 'from', 'to'] as $req) {
                if(empty($data[ $req ])) {
                    return "{$req} is required";
                }
            }
    
            $from   = strtoupper($data['from'] ?? "");
            $to     = strtoupper($data['to'] ?? "");
            $amount = floatval($data['amount']);
            $rate   = 0;
    
            if(empty($from) || empty($to)) {
                return "Invalid From & To Currency";
            }
    
            if($amount <= 0) {
                return "Invalid Amount";
            }

            if($from == $to) {
                return [
                    'amount' => $amount,
                    'rate' => 1
                ];
            }
    
            /** Get Real Account */
            $idAcc = is_int($data['account_id'])? $data['account_id'] : 0;
            $realAccount = self::realAccountDetail(md5(md5($data['account_id'])));
            if(empty($realAccount)) {
                return "Invalid Account";
            }
    
            switch($realAccount['RTYPE_ISFLOATING']) {
                case 1:
                    /** Jika akun floating */
                    /** Pertama cek apakah ada manual configurasi dari menu tools -> rate */
                    $manualRate = Helper::getManualConfigurationRate($from, $to);
                    if($manualRate != 0) {
                        $rate = $manualRate;
                    }else {
                        // $rate = $helperClass->getFloatingRate($from, $to);
                        $rate = Helper::getFloatingRate_jisdor($from, $to);
                    }
                    
                    break;
    
                case 0: 
                    /** Jika bukan akun floating */
                    $rate = $realAccount['RTYPE_RATE'];
                    break;
            }
    
            /** Check Rate */
            if(is_numeric($rate) === FALSE || $rate <= 0) {
                return "Failed to get floating rate";
            }
           
            return [
                'amount' => $amount,
                'rate' => $rate
            ];

        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function haveStandartAccount(string $userid) {
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
                    ACC_INVESTOR
                FROM tb_racc 
                JOIN tb_racctype ON (ID_RTYPE = ACC_TYPE)
                WHERE MD5(MD5(ACC_MBR)) = '{$userid}' 
                AND ACC_DERE = 1 
                AND ACC_LOGIN != 0
                AND ACC_WPCHECK = 6
                AND UPPER(RTYPE_TYPE) IN ('STANDARD', 'PRO')
            ");

            if($sqlGet->num_rows == 0) {
                return [];
            }

            return $sqlGet->fetch_all(MYSQLI_ASSOC);

        } catch (Exception $e) {
            return [];
        }
    }

    public static function getDemoAccount(string $userid): array {
        try {
            global $db;
            $sqlGet = $db->query("
                SELECT 
                    tr.*,
                    IFNULL(mtud.BALANCE, 10000) as BALANCE,
                    mtud.CREDIT,
                    mtud.EQUITY,
                    mtud.MARGIN,    
                    mtud.MARGIN_FREE as FREE_MARGIN,
                    mtud.LEVERAGE
                FROM tb_racc tr 
                JOIN tb_racctype tra ON (tra.ID_RTYPE = tr.ACC_TYPE) 
                LEFT JOIN mt5_users mtud ON (mtud.LOGIN = tr.ACC_LOGIN)
                WHERE UPPER(tra.RTYPE_TYPE) = 'DEMO'
                AND MD5(MD5(tr.ACC_MBR)) = '{$userid}' 
                LIMIT 1
            ");

            return $sqlGet->fetch_assoc() ?? [];

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function getProgressRealAccount(string $userid): array  {
        try {
            global $db;
            $sqlGet = $db->query("
                SELECT 
                    tr.*,
                    tra.*
                FROM tb_racc tr 
                JOIN tb_racctype tra ON (tra.ID_RTYPE = tr.ACC_TYPE) 
                WHERE UPPER(tra.RTYPE_TYPE) != 'DEMO'
                AND tr.ACC_STS IN (0, 1, 2)
                AND MD5(MD5(tr.ACC_MBR)) = '{$userid}'
                ORDER BY ID_ACC DESC 
                LIMIT 1
            ");

            return $sqlGet->fetch_assoc() ?? [];

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function getProgressRealAccount_byID(string $idACc): array  {
        try {
            global $db;
            $sqlGet = $db->query("
                SELECT 
                    tr.*,
                    tra.*,
                    tm.*,
                    tacc.*,
                    tib.MBR_NAME as IB_NAME,
                    tib.MBR_EMAIL as IB_EMAIL
                FROM tb_racc tr 
                JOIN tb_member tm ON (tm.MBR_ID = tr.ACC_MBR)
                JOIN tb_racctype tra ON (tra.ID_RTYPE = tr.ACC_TYPE)
                LEFT JOIN tb_acccond tacc ON (tacc.ACCCND_ACC = tr.ID_ACC AND tacc.ACCCND_STS != 1)
                LEFT JOIN tb_member tib ON (tib.MBR_ID = tacc.ACCCND_IB)
                WHERE UPPER(tra.RTYPE_TYPE) != 'DEMO'
                AND MD5(MD5(tr.ID_ACC)) = '{$idACc}'
                LIMIT 1
            ");

            return $sqlGet->fetch_assoc() ?? [];

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function getAccountHistoryNote(int $idAcc): array  {
        try {
            global $db;
            $sqlGet = $db->query("
                SELECT 
                    tn.*
                FROM tb_note tn
                JOIN tb_racc tr ON (tr.ID_ACC = tn.NOTE_RACC)
                WHERE tr.ID_ACC = {$idAcc}
                ORDER BY tn.ID_NOTE DESC
            ");

            return $sqlGet->fetch_all(MYSQLI_ASSOC) ?? [];

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function getLastAccount(string $userid) {
        try {
            global $db;
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
                return [];
            }

            return $sqlGet->fetch_assoc();

        } catch (Exception $e) {
            return [];
        }
    }

    public static function duplicateLastAccount(string $userid) {
        try {
            global $db;
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

            /** Sql Insert New Record */
            $insert = Database::insert("tb_racc", $lastAccount);
            return $insert;

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    }

    public static function allowToApplyReferral(string $userid): bool  {
        try {
            global $db;
            /**
             * Syarat
             * 1. Belum memiliki Upline / Upline Bukan Admin
             * 2. Belum create real account maupun progress real account
             */

            $sqlGet = $db->query("
                SELECT 
                    tm.MBR_IDSPN,
                    COUNT(tr.ID_ACC) as TOTAL_ACC
                FROM tb_member tm
                LEFT JOIN (
                    SELECT
                        ID_ACC,
                        ACC_MBR,
                        ACC_LOGIN
                    FROM tb_racc
                    JOIN tb_racctype ON (ID_RTYPE = ACC_TYPE)
                    WHERE UPPER(RTYPE_TYPE) != 'DEMO'
                ) as tr ON (tr.ACC_MBR = tm.MBR_ID)
                WHERE MD5(MD5(MBR_ID)) = '{$userid}'
                GROUP BY tm.MBR_ID
                LIMIT 1
            ");

            if($sqlGet->num_rows != 1) {
                return false;
            }

            $detail = $sqlGet->fetch_assoc();
            if($detail['TOTAL_ACC'] != 0) {
                return false;
            }

            return (empty($detail['MBR_IDSPN']) || $detail['MBR_IDSPN'] == 1000000000)
                ? true
                : false;

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    }

    public static function getAvailableProduct(string $userid, string $type = ""): array {
        try {
            global $db;
            $sqlGet = $db->query("
                SELECT 
                    tm.MBR_ID,
                    tm.MBR_NAME,
                    tra.*
                FROM tb_member tm
                LEFT JOIN (
                    SELECT 
                        ID_RTYPE,
                        RTYPE_SUFFIX,
                        RTYPE_NAME,
                        RTYPE_TYPE_AS,
                        RTYPE_TYPE,
                        RTYPE_RATE,
                        RTYPE_CURR,
                        RTYPE_LEVERAGE,
                        RTYPE_STS,
                        RTYPE_KOMISI
                    FROM tb_racctype
                    WHERE UPPER(RTYPE_TYPE) != 'DEMO'
                ) as tra ON ((tra.RTYPE_SUFFIX = tm.MBR_SUFFIX OR tm.MBR_SUFFIX IS NULL) AND tra.RTYPE_STS = -1)
                WHERE MD5(MD5(MBR_ID)) = '{$userid}'
                AND (tm.MBR_SUFFIX_EXCLUDE IS NULL OR tm.MBR_SUFFIX_EXCLUDE NOT LIKE CONCAT('%', tra.RTYPE_SUFFIX, '%'))
                GROUP BY tra.ID_RTYPE
            ");

            if($sqlGet->num_rows == 0) {
                return [];
            }

            $products = [];
            foreach($sqlGet->fetch_all(MYSQLI_ASSOC) as $product) {
                if(!empty($type)) {
                    if(strtoupper($product['RTYPE_TYPE_AS']) != strtoupper($type)) {
                        continue;
                    }
                }

                $productType = strtolower($product['RTYPE_TYPE']);
                $index = array_search($productType, array_column($products, "type"));
                if($index === FALSE) {
                    $products[] = [
                        'type' => $productType
                    ];

                    $index = array_search($productType, array_column($products, "type"));
                }

                $products[ $index ]['products'][] = $product;
            }

            return $products;
           

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function checkAccountSuffix(string $suffix): array {
        try {
            global $db;
            $sqlGet = $db->query("SELECT * FROM tb_racctype WHERE RTYPE_SUFFIX = '{$suffix}' LIMIT 1");
            if($sqlGet->num_rows != 1) {
                return [];
            }

            return $sqlGet->fetch_assoc();

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function getDepositNewAccount_data(int $idAcc) {
        try {
            global $db;
            $sqlGet = $db->query("
                SELECT 
                    td.*
                FROM tb_dpwd td
                JOIN tb_racc tr ON (tr.ID_ACC = td.DPWD_RACC)
                WHERE tr.ID_ACC = {$idAcc}
                AND td.DPWD_TYPE = 3
                AND td.DPWD_STS != 1
                ORDER BY td.ID_DPWD DESC
                LIMIT 1
            ");

            return $sqlGet->fetch_assoc() ?? [];

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function getDepositNewAccount_History(int $idAcc) {
        try {
            global $db;
            $sqlGet = $db->query("
                SELECT 
                    td.*,
                    tn.*
                FROM tb_dpwd td
                JOIN tb_racc tr ON (tr.ID_ACC = td.DPWD_RACC)
                JOIN tb_note tn ON (tn.NOTE_RACC = tr.ID_ACC AND tn.NOTE_DPWD = td.ID_DPWD)
                WHERE tr.ID_ACC = {$idAcc}
                AND td.DPWD_TYPE = 3
                ORDER BY td.ID_DPWD DESC
            ");

            return $sqlGet->fetch_all(MYSQLI_ASSOC) ?? [];

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

    public static function havePendingTransaction(int $mbrid, array $dpwd_type = [1]) {
        try {
            global $db;
            $type = implode(",", $dpwd_type);
            $sqlGet = $db->query("SELECT ID_DPWD FROM tb_dpwd WHERE DPWD_MBR = {$mbrid} AND DPWD_TYPE IN ({$type}) AND DPWD_STS = 0");
            return ($sqlGet->num_rows != 0);

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    }

    public static function accountCommission() {
        try {
            global $db;
            $sqlGet = $db->query("SELECT RTYPE_KOMISI FROM tb_racctype WHERE UPPER(RTYPE_TYPE) != 'DEMO' AND RTYPE_KOMISI > 0 GROUP BY RTYPE_KOMISI ORDER BY RTYPE_KOMISI");
            if($sqlGet->num_rows == 0) {
                return [];
            }

            return array_map(fn($ar): int => $ar['RTYPE_KOMISI'], $sqlGet->fetch_all(MYSQLI_ASSOC));

        } catch (Exception $e) {
            return [];
        }
    }

    public static function generatePassword(int $len = 5): string {
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

    public static function all(int $mbrid) {
        try {
            $db = Database::connect();
            $sqlGet = $db->query("SELECT * FROM tb_racc JOIN tb_racctype ON (ID_RTYPE = ACC_TYPE) WHERE ACC_MBR = $mbrid AND ACC_DERE = 1 AND ACC_LOGIN != '0'");
            return $sqlGet->fetch_all(MYSQLI_ASSOC) ?? [];

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
        // $apiMeta        = ApiMetatrader();
        // $tokenManager   = $apiMeta->token_manager_demo;
    }

    public static function myAccount(int $mbrid) {
        try {
            $db = Database::connect();
            $sqlGet = $db->query("SELECT * FROM tb_racc  JOIN tb_racctype ON (ID_RTYPE = ACC_TYPE) WHERE ACC_MBR = $mbrid AND ACC_DERE = 1 AND ACC_STS = -1");
            return $sqlGet->fetch_all(MYSQLI_ASSOC) ?? [];

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [];
        }
    }

}