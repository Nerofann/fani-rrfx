<?php
namespace App\Models;

use Config\Core\SystemInfo;
use Exception;

class Ib {

    public static $userType = [
        '1' => "Mib",
        '2' => "Ib",
        '3' => "Trader"
    ];

    public static function isAllowToBecomeIb(string $mbridHash): bool {
        try {
            /** 
             * Persyaratan
             * - Memiliki Standard Account
             * - Memiliki setidaknya $100 margin free di salah satu account (bukan summary)
             * 
            *  */

            $standartAccount = Account::haveStandartAccount($mbridHash);
            if(empty($standartAccount)) {
                return false;
            }

            foreach($standartAccount as $sac) {
                if($sac['MARGIN_FREE'] >= 100) {
                    return true;
                }
            }

            return false;

        } catch (Exception $e) {
            if(SystemInfo::isDevelopment()) {
                throw $e;
            }

            return false;
        }
    }
}