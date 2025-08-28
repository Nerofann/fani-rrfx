<?php

use App\Models\AccountTrade;

$accountId = form_input($_POST['account_id']) ?? "";
if(empty($accountId)) {
    ApiResponse([
        'status' => false,
        'message' => "Account Id is required",
        'response' => []
    ], 400);
}

$password = form_input($_POST['password']) ?? "";
if(empty($password)) {
    ApiResponse([
        'status' => false,
        'message' => "Password is required",
        'response' => []
    ], 400);
}

/** Check Account */
$account = $classAcc->realAccountDetail($accountId);
if(empty($account)) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Account",
        'response' => []
    ], 400);
}

$accountTrade = AccountTrade::get($account['ACC_LOGIN']);
if(!$accountTrade) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Trade Account",
        'response' => []
    ], 400);
}

$update = $helperClass->updateWithArray("tb_racc_trade", ['ACCTRADE_PASS' => $password], ['ACCTRADE_LOGIN' => $account['ACC_LOGIN']]);
if(!$update) {
    ApiResponse([
        'status' => false,
        'message' => "Failed to update account",
        'response' => []
    ], 400);
}

ApiResponse([
    'status' => true,
    'message' => "Account updated successfully",
    'response' => []
], 200);    