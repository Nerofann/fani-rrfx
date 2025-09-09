<?php

use App\Models\Account;

$accounts = Account::myAccount($user['MBR_ID']);
if(empty($accounts)) {
    ApiResponse([
        'status' => false,
        'message' => "Account not found",
        'response' => []
    ], 400);
}

$result = [];
foreach($accounts as $acc) {
    $result[] = [
        'login' => intval($acc['ACC_LOGIN']),
        'balance' => floatval($acc['MARGIN_FREE']),
    ];
}

ApiResponse([
    'status' => true,
    'message' => "Success",
    'response' => $result
], 200);
