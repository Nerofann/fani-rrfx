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

/** Check Account */
$account = $classAcc->realAccountDetail($accountId);
if(empty($account) || $account['ACC_MBR'] != $userData['MBR_ID']) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Account",
        'response' => []
    ], 400);
}

/** Check account trade */
$accountTrade = AccountTrade::get($account['ACC_LOGIN']);
if($accountTrade) {
    ApiResponse([
        'status' => false,
        'message' => "Account already exists",
        'response' => []
    ], 400);
}

/** Insert to tb_racc_trade */
/** ACCTRADE_SERVER otomatis diisi ICDX-Demo, jika ingin diganti, silahkan ganti default value kolom ACCTRADE_SERVER di mysql, jangan isi disini */
$insert = $helperClass->insertWithArray("tb_racc_trade", [
    'ACCTRADE_MBR' => $account['ACC_MBR'],
    'ACCTRADE_LOGIN' => $account['ACC_LOGIN'],
    'ACCTRADE_PASS' => $account['ACC_PASS'],
    'ACCTRADE_TOKEN' => $account['ACC_TOKEN'],
    'ACCTRADE_DATETIME' => date("Y-m-d H:i:s"),
]);

if(!$insert) {
    ApiResponse([
        'status' => false,
        'message' => "Failed to add account",
        'response' => []
    ], 400);
}

ApiResponse([
    'status' => true,
    'message' => "Account added successfully",
    'response' => []
], 200);
