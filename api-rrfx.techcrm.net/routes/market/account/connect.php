<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;
use Config\Core\Database;

$accountLogin = Helper::form_input($_POST['account'] ?? 0);
$apiTerminal = MetatraderFactory::apiTerminal();

/** Check Account */
$account = Account::realAccountDetail_byLogin($accountLogin);
if(empty($account) || $account['ACC_MBR'] != $user['MBR_ID']) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Account",
        'response' => []
    ], 400);
}

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
            ApiResponse([
                'status' => false,
                'message' => "Invalid Connection",
                'response' => []
            ], 404);
        }

        Database::update("tb_racc", ['ACC_TOKEN' => $token], ['ID_ACC' => $account['ID_ACC']]);
        break;

    case false:
        /** check connection with available token */
        $account = $apiTerminal->accountSummary(['id' => $account['ACC_TOKEN']]);
        if(!$account->success) {
            /** get new token */
            $connectData = [
                'login' => $account['ACC_LOGIN'], 
                'password' => $account['ACC_PASS']
            ];
            
            $token = $apiTerminal->connect($connectData);
            if(!$token) {
                ApiResponse([
                    'status' => false,
                    'message' => "Invalid Connection (2)",
                    'response' => []
                ], 404);
            }

            Database::update("tb_racc", ['ACC_TOKEN' => $token], ['ID_ACC' => $account['ID_ACC']]);
        }

    default: break;
}


ApiResponse([
    'status' => true,
    'message' => "Successfull",
    'response' => [
        // 'token' => $token
    ]
]);