<?php
namespace App\Factory;

use Allmedia\Shared\Metatrader\ApiManager;
use Allmedia\Shared\Metatrader\ApiTerminal;
use App\Models\Account;
use Config\Core\Database;
use Config\Core\SystemInfo;
use Exception;

class MetatraderFactory {

    public static $serverReal = "RRFX-Live";
    public static $serverDemo = "RRFX-Demo";
    public static $tokenManagerDemo = "5c585515-6500-4fc2-b7fb-ca3a7c276d4c-rrfx-demo";
    public static $tokenManagerReal = "5c585515-6500-4fc2-b7fb-ca3a7c276d4c-rrfx-live";
    public static float $initMarginDemo = 10000;

    public static function apiManager(): ApiManager {
        return new ApiManager(self::$tokenManagerReal, "http://139.180.212.62:7005");
    }

    public static function apiTerminal(): ApiTerminal {
        return new ApiTerminal(self::$serverReal);
    }

    public static function apiTerminalDemo(): ApiTerminal {
        return new ApiTerminal(self::$serverDemo);
    }

    public static function apiManagerDemo(): ApiManager {
        return new ApiManager(self::$tokenManagerDemo, "http://139.180.212.62:7005");
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

}