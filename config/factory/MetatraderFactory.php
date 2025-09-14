<?php
namespace App\Factory;

use Allmedia\Shared\Metatrader\ApiManager;
use Allmedia\Shared\Metatrader\ApiTerminal;
use App\Models\Account;
use Config\Core\Database;
use Config\Core\SystemInfo;
use Exception;

class MetatraderFactory {

    public static $serverReal = "";
    public static $serverDemo = "";
    public static $tokenManagerDemo = "5c585515-6500-4fc2-b7fb-ca3a7c276d4c-rrfx-demo";
    public static $tokenManagerReal = "02c89495-67a8-4ca0-9a81-af0d25e74135-rrfx-live";
    public static float $initMarginDemo = 10000;

    public static function credential() {
        return [
            'tokenManagerDemo' => ($_ENV['MANAGER_TOKEN_DEMO'] ?? ""),
            'tokenManagerLive' => ($_ENV['MANAGER_TOKEN_LIVE'] ?? ""),
            'serverDemo' => ($_ENV['SERVER_NAME_DEMO'] ?? ""),
            'serverLive' => ($_ENV['SERVER_NAME_LIVE'] ?? ""),
            'managerEndpoint' => ($_ENV['MANAGER_ENDPOINT'] ?? ""),
        ];
    }

    public static function apiManager(): ApiManager {
        $credential = self::credential();
        return new ApiManager($credential['tokenManagerLive'], $credential['managerEndpoint']);
    }

    public static function apiTerminal(): ApiTerminal {
        $credential = self::credential();
        return new ApiTerminal($credential['serverLive']);
    }

    public static function apiTerminalDemo(): ApiTerminal {
        $credential = self::credential();
        return new ApiTerminal($credential['serverDemo']);
    }

    public static function apiManagerDemo(): ApiManager {
        $credential = self::credential();
        return new ApiManager($credential['tokenManagerDemo'], $credential['managerEndpoint']);
    }

    public static function createDemo(?string $fullname, ?string $email): array {
        try {
            /** Get Demo Type */
            $db = Database::connect();
            $sqlGetType = $db->query("SELECT ID_RTYPE, RTYPE_GROUP, RTYPE_LEVERAGE FROM tb_racctype WHERE UPPER(RTYPE_TYPE) = 'DEMO' LIMIT 1");
            $demoType = $sqlGetType->fetch_assoc() ?? [];
            if($sqlGetType->num_rows == 0 || empty($demoType)) {
                return [
                    'success' => false,
                    'message' => "Invalid Demo Account",
                    'data' => []
                ];
            }

            /** check type */
            $meta_pass = Account::generatePassword();
            $meta_investor = Account::generatePassword();
            $meta_phone = Account::generatePassword();

            /** Create Demo */
            $apiManager = self::apiManagerDemo();
            $apiData = [
                'master_pass' => $meta_pass, 
                'investor_pass' => $meta_investor, 
                'group' => $demoType['RTYPE_GROUP'], 
                'fullname' => ($fullname ?? "-"), 
                'email' => ($email ?? "-"), 
                'leverage' => $demoType['RTYPE_LEVERAGE'],
                'comment' => "metaapi"
            ];

            $createDemo = $apiManager->createAccount($apiData);
            if(!is_object($createDemo) || !property_exists($createDemo, "Login")) {
                return [
                    'success' => false,
                    'message' => "Gagal membuat akun demo",
                    'data' => []
                ];
            }

            /** deposit demo margin */
            $deposit = $apiManager->deposit([
                'login' => $createDemo->Login,
                'amount' => self::$initMarginDemo,
                'comment' => "metaapi"
            ]);

            return [
                'success' => true,
                'message' => "Successfull",
                'data' => [
                    'login' => $createDemo->Login,
                    'password' => $meta_pass,
                    'investor' => $meta_investor,
                    'passphone' => $meta_phone,
                    'type' => $demoType['ID_RTYPE']
                ]
            ];


        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return [
                'success' => false,
                'message' => "Internal Server Error",
                'data' => []
            ];
        }
    }

    public static function autoConnect(int $login): string|bool {
        try {
            /** Check Account */
            $account = Account::realAccountDetail_byLogin($login);
            if(empty($account)) { 
                return false;
            }

            $apiTerminal = self::apiTerminal();
            $isEmptyToken = empty($account['ACC_TOKEN']);
            $token = "";
            switch($isEmptyToken) {
                case true:
                    /** Connect meta */
                    $connectData = [
                        'login' => $account['ACC_LOGIN'], 
                        'password' => $account['ACC_PASS']
                    ];
                    
                    $token = $apiTerminal->connect($connectData);
                    if(!$token) {
                        return false;
                    }

                    Database::update("tb_racc", ['ACC_TOKEN' => $token], ['ID_ACC' => $account['ID_ACC']]);
                    break;

                case false:
                    /** check connection with available token */
                    $token = $account['ACC_TOKEN'];
                    $summary = $apiTerminal->accountSummary(['id' => $account['ACC_TOKEN']]);
                    if(!$summary->success) {
                        /** get new token */
                        $connectData = [
                            'login' => $account['ACC_LOGIN'], 
                            'password' => $account['ACC_PASS']
                        ];
                        
                        $token = $apiTerminal->connect($connectData);
                        if(!$token) {
                            return false;
                        }

                        Database::update("tb_racc", ['ACC_TOKEN' => $token], ['ID_ACC' => $account['ID_ACC']]);
                    }


                default: break;
            }

            return $token;

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    }

}