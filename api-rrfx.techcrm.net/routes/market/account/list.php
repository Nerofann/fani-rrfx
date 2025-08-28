<?php

use App\Models\AccountTrade;

$accountTrade = AccountTrade::list($userData['MBR_ID']);
$accounts = [];
if(empty($accountTrade)) {
    ApiResponse([
        'status' => false,
        'message' => "Account not found",
        'response' => []
    ], 400);
}

$logins = array_map(fn($account) => intval($account['ACCTRADE_LOGIN']), $accountTrade);
$accountDetail = $ApiMeta->accountGroupLogin(['logins' => $logins]);
if(!$accountDetail->success) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Account Detail",
        'response' => []
    ], 400);
}

foreach($accountDetail->message as $account) {
    $searchId = array_search($account->Login, array_column($accountTrade, 'ACCTRADE_LOGIN'));
    if($searchId !== false) {
        $accounts[] = [
            'id' => md5(md5($accountTrade[$searchId]['ID_ACCTRADE'])),
            'login' => intval($account->Login),
            'balance' => floatval($account->Balance),
        ];
    }
}

ApiResponse([
    'status' => true,
    'message' => "Success",
    'response' => $accounts
], 200);
