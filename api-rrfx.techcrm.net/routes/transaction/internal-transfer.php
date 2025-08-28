<?php
$data = $helperClass->getSafeInput($_POST);
$required = [
    'acc_from' => "Nomor Login Akun Pengirim",
    'acc_to' => "Nomor Login Akun Penerima",
    'amount' => "Jumlah",
];

foreach($required as $req => $text) {
    if(empty($data[ $req ])) {
        ApiResponse([
            'status'    => false,
            'message'   => "Kolom {$text} tidak boleh kosong",
            'response'  => []
        ], 400);
    }
}

/** Check Amount */
$amount = $helperClass->stringTonumber($data['amount']);
if(is_numeric($amount) === FALSE || $amount <= 0) {
    ApiResponse([
        'status'    => false,
        'message'   => "Jumlah transfer tidak valid",
        'response'  => []
    ], 400);
}

/** validasi nomor login */
$loginPengirim = ((int) $data['acc_from']) ?? 0;
$loginPenerima = ((int) $data['acc_to']) ?? 0;

/** Check Nomor login apakah sama antara pengirim dan penerima */
if($loginPengirim == $loginPenerima) {
    ApiResponse([
        'status'    => false,
        'message'   => "Login pengirim dan penerima tidak boleh sama",
        'response'  => []
    ], 400);
}

/** Check Account */
$userAccount = myAccount($userId, "real");
$availableAccounts = array_map(fn($ar): string => $ar['ACC_LOGIN'], $userAccount);

/** Check akun Pengirim */
if(!in_array($loginPengirim, $availableAccounts)) {
    ApiResponse([
        'status'    => false,
        'message'   => "Login pengirim tidak valid",
        'response'  => []
    ], 400);
}

/** Check Balance pengirim */
$detailAkunPengirim = $ApiMeta->accountDetails(['login' => $loginPengirim]);
if(!$detailAkunPengirim->success) {
    ApiResponse([
        'status'    => false,
        'message'   => "Login pengirim tidak terdaftar",
        'response'  => []
    ], 400);
}

$balancePengirim = $detailAkunPengirim->message->Balance;
if($balancePengirim < $amount) {
    ApiResponse([
        'status'    => false,
        'message'   => "Balance Login {$loginPengirim} tidak mencukupi",
        'response'  => []
    ], 400);
}

/** Check Penerima */
if(!in_array($loginPenerima, $availableAccounts)) {
    ApiResponse([
        'status'    => false,
        'message'   => "Login penerima tidak valid",
        'response'  => []
    ], 400);
}

/** Check Rate */
$detailPenerima = [];
$detailPengirim = [];
foreach($userAccount as $acc) {
    if($acc['ACC_LOGIN'] == $loginPengirim) {
        $detailPengirim = $acc;
    
    }else if($acc['ACC_LOGIN'] == $loginPenerima) {
        $detailPenerima = $acc;
    }
}

if(empty($detailPenerima) || empty($detailPengirim)) {
    ApiResponse([
        'status'    => false,
        'message'   => "Invalid Accounts",
        'response'  => []
    ], 400);
}

/** Check Rate */
if($detailPengirim['RTYPE_RATE'] != $detailPenerima['RTYPE_RATE']) {
    ApiResponse([
        'status'    => false,
        'message'   => "Rate akun tidak sama",
        'response'  => []
    ], 400);
}

/** Adjustment Account Rate */
$amountSource = $amount;
// $adjustment = $this->internalTransfer_adjustmentRate($detailPengirim, $detailPenerima, $amount);
// $amount = round($adjustment['amount'], 2);

/** Start Transaction */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($db);

/** Insert */
$internalTransfer = $helperClass->insertWithArray("tb_internal_transfer", [
    'IT_FROM'   => $loginPengirim,
    'IT_TO' => $loginPenerima,
    'IT_AMOUNT' => $amount,
    'IT_AMOUNT_SOURCE'  => $amountSource,
    'IT_CURR_TO'    => "USD",
    'IT_CURR_FROM'  => "USD",
    'IT_RATE_TO'    => $detailPengirim['RTYPE_RATE'],
    'IT_RATE_FROM'  => $detailPenerima['RTYPE_RATE'],
    // 'IT_RATE_TO'    => $adjustment['rate_penerima'],
    // 'IT_RATE_FROM'  => $adjustment['rate_pengirim'],
    'IT_DATETIME'   => date("Y-m-d H:i:s")
]);

if(!$internalTransfer) {
    ApiResponse([
        'status'    => false,
        'message'   => "Internal Transfer gagal",
        'response'  => []
    ], 400);
}

/** Last Insert ID */
$idInternalTransfer = $db->insert_id;

/** Withdraw balance pengirim */
$wdBalance = $ApiMeta->withdrawal(['login' => $loginPengirim, 'amount' => $amountSource, 'comment' => "ITOUT-{$idInternalTransfer}"]);
if(!$wdBalance->success) {
    $db->rollback();
    ApiResponse([
        'status'    => false,
        'message'   => "[WD] ".($wdBalance->error ?? "Failed Withdrawal"),
        'response'  => []
    ], 400);
}

/** Deposit balance penerima */
$depositBalance = $ApiMeta->deposit(['login' => $loginPenerima, 'amount' => $amount, 'comment' => "ITIN-{$idInternalTransfer}"]);
if(!$depositBalance->success) {
    $db->rollback();
    ApiResponse([
        'status'    => false,
        'message'   => "[DP] ".($depositBalance->error ?? "Failed Deposit"),
        'response'  => []
    ], 400);
}

/** Update data internal_transfer */
$update = $helperClass->updateWithArray("tb_internal_transfer", [
    'IT_TICKET_FROM' => ($wdBalance->message->ticket ?? 0),
    'IT_TICKET_TO' => ($depositBalance->message->ticket ?? 0)
], [
    'ID_IT' => $idInternalTransfer
]);

newInsertLog([
    'mbrid' => $user['MBR_ID'],
    'module' => "internal-transfer",
    'ref' => $idInternalTransfer,
    'message' => "Internal Transfer {$amount} USD from {$loginPengirim} to {$loginPenerima}",
    'device' => "mobile",
    'ip' => $helperClass->get_ip_address(),
    'data' => json_encode($data)
]);

$db->commit();
ApiResponse([
    'status'    => true,
    'message'   => "Transfer Berhasil",
    'response'  => [
        'id'    => md5(md5($idInternalTransfer))
    ]
]);