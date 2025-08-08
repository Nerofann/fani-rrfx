<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;
use Config\Core\Database;

$apiTerminal = MetatraderFactory::apiTerminal();
$login = Helper::form_input($_POST['account'] ?? "");
$account = Account::realAccountDetail_byLogin($login);
if(!$account) {
    JsonResponse([
        'success' => false,
        'message' => "Mohon pilih akun",
        'data' => []
    ]);
}

/** check Pakai acc_token */
// if(!empty($account['ACC_TOKEN'])) {
//     $token = $apiTerminal->connect(['login' => $account]);
// }

/** Connect */
$token = $apiTerminal->connect(['login' => $account['ACC_LOGIN'], 'password' => $account['ACC_PASS']]);
if(!$token) {
    JsonResponse([
        'success' => false,
        'message' => "Connection Failed",
        'data' => []
    ]);
}

/** get Symbols */

Database::update("tb_racc", ['ACC_TOKEN' => $token], ['ID_ACC' => $account['ID_ACC']]);
JsonResponse([
    'success' => true,
    'message' => "Berhasil",
    'data' => []
]);