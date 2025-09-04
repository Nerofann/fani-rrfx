<?php
namespace App\Factory;

use Allmedia\Shared\Metatrader\ApiManager;
use Allmedia\Shared\Metatrader\ApiTerminal;
use App\Models\Account;
use Config\Core\Database;
use Config\Core\SystemInfo;
use Exception;

class MetatraderFactory {

    public static $server = "ICDX-Demo";
    public static $tokenManager = "47c8fe4e-4733-4fe3-bdcf-5c2a97e9dca5";
    public static float $initMarginDemo = 10000;

    public static function apiManager(): ApiManager {
        return new ApiManager(self::$tokenManager);
    }

    public static function apiTerminal(): ApiTerminal {
        return new ApiTerminal(self::$server);
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
            $apiManager = self::apiManager();
            $apiData = [
                'master_pass' => $meta_pass, 
                'investor_pass' => $meta_investor, 
                'group' => "demo\MandiriInvestindo\MMUSD", 
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