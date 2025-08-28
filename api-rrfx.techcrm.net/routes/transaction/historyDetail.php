<?php

$dpwdId = form_input($_GET['id'] ?? "-");
$sqlGet = $db->query("
    SELECT 
        td.ID_DPWD,
        td.DPWD_MBR,
        td.DPWD_TYPE,
        td.DPWD_BANKSRC,
        td.DPWD_BANK,
        td.DPWD_AMOUNT,
        td.DPWD_AMOUNT_SOURCE,
        td.DPWD_CURR_FROM,
        td.DPWD_CURR_TO,
        td.DPWD_STS,
        td.DPWD_DATETIME,
        tr.ACC_LOGIN
    FROM tb_dpwd td
    JOIN tb_racc tr ON (tr.ID_ACC = td.DPWD_RACC)
    WHERE MD5(MD5(td.ID_DPWD)) = '{$dpwdId}'
    ORDER BY td.DPWD_DATETIME DESC
");

if($sqlGet->num_rows != 1) {
    ApiResponse([
        'success' => false,
        'message' => "Invalid ID",
        'response' => []
    ]);
}

$dpwd = $sqlGet->fetch_assoc();
switch($dpwd['DPWD_TYPE']) {
    case 1: $type = "Deposit"; break;
    case 2: $type = "Withdrawal"; break;
    case 3: $type = "Deposit New Account"; break;
    default: $type = "-";
}

switch($dpwd['DPWD_STS']) {
    case 0: $status = "pending"; break;
    case -1: $status = "success"; break;
    case 1: $status = "reject"; break;
    default: $status = "-";
}

$amount = $dpwd['DPWD_CURR_FROM'] ." ". $helperClass->formatCurrency($dpwd['DPWD_AMOUNT_SOURCE']) ?? 0;
$amountReceived = $dpwd['DPWD_CURR_TO'] ." ". $helperClass->formatCurrency($dpwd['DPWD_AMOUNT']) ?? 0;

$bankUser = explode("/", $dpwd['DPWD_BANKSRC']);
$bankAdmin = explode("/", $dpwd['DPWD_BANK']); 

ApiResponse([
    'status' => true,
    'message' => "Success",
    'response' => [
        'id' => md5(md5($dpwd['ID_DPWD'])),
        'type' => $type,
        'login' => $dpwd['ACC_LOGIN'],
        'amount' => $amount,
        'amount_received' => $amountReceived,
        'status' => $status,
        'datetime' => date("Y-m-d H:i:s", strtotime($dpwd['DPWD_DATETIME'])),
        'bank_user' => [
            'name' => $bankUser[0] ?? "-",
            'account_number' => $bankUser[1] ?? 0,
            'account_name' => $bankUser[2]
        ],
        'bank_admin' => [
            'name' => $bankAdmin[0] ?? "-",
            'account_number' => $bankAdmin[1] ?? 0,
            'account_name' => $bankAdmin[2]
        ]
    ]
]);