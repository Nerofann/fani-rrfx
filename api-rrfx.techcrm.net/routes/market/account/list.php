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

foreach($accounts as $acc) {
    $accounts[] = [
        'login' => intval($acc['ACC_LOGIN']),
        'balance' => floatval($acc['FREE_MARGIN']),
    ];
}

ApiResponse([
    'status' => true,
    'message' => "Success",
    'response' => $accounts
], 200);
