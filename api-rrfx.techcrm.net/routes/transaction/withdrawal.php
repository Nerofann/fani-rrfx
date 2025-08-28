<?php
$data = $helperClass->getSafeInput($_POST);
$required = [
    'account' => "Real Account",
    'bank_user' => "Bank User",
    'amount' => "Jumlah Withdrawal",
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

/** Validasi Amount  */
$amountSource = $helperClass->stringTonumber($data['amount']);
if($amountSource <= 0) {
    ApiResponse([
        'status'    => false,
        'message'   => "Jumlah deposit tidak valid",
        'response'  => []
    ], 400);
}

/** Validasi bank user */
$bankUser = myBank($userId, $data['bank_user']);
if(empty($bankUser)) {
    ApiResponse([
        'status'    => false,
        'message'   => "Bank User tidak valid / ditemukan",
        'response'  => []
    ], 400);
}

/** Check account */
$account = $classAcc->realAccountDetail($data['account']);
if(empty($account)) {
    ApiResponse([
        'status'    => false,
        'message'   => "Real Account tidak valid / ditemukan",
        'response'  => []
    ], 400);
}

/** Check ACC_MBR */
if($account['ACC_MBR'] != $userData['MBR_ID']) {
    ApiResponse([
        'status'    => false,
        'message'   => "Permintaan ditolak",
        'response'  => []
    ], 400);
}

/** Check Minimum Withdrawal */
$amountCheck =  $amountSource;
if($amountCheck < $account['RTYPE_MINWITHDRAWAL']) {
    ApiResponse([
        'status'    => false,
        'message'   => "Minimum Withdrawal ".$helperClass->formatCurrency($account['RTYPE_MINWITHDRAWAL']) . " ".$account['RTYPE_CURR'],
        'response'  => []
    ], 400);
}

/** Check Maximum Withdrawal */
$amountCheck =  $amountSource;
if($amountCheck < $account['RTYPE_MAXWITHDRAWAL']) {
    ApiResponse([
        'status'    => false,
        'message'   => "Maximum Withdrawal ".$helperClass->formatCurrency($account['RTYPE_MAXWITHDRAWAL']) . " ".$account['RTYPE_CURR'],
        'response'  => []
    ], 400);
}

/** cek Withdrawal Pending */
if($classAcc->havePendingTransaction($userData['MBR_ID'], [2]) !== FALSE) {
    ApiResponse([
        'status'    => false,
        'message'   => "Masih ada transaksi withdrawal dengan status pending",
        'response'  => []
    ], 400);
}

/** conversation */
$convert = $classAcc->accountConvertation([
    'account_id' => $account['ID_ACC'],
    'amount' => $amountSource,
    'from' => $account['RTYPE_CURR'],
    'to' => "IDR"
]);

if(!is_array($convert)) {
    ApiResponse([
        'status'    => false,
        'message'   => $convert,
        'response'  => []
    ], 400);
}

/** Set Amount Final */
$amountFinal = ($amountSource * $convert['rate']);

/** Insert DPWD */
$insert = $helperClass->insertWithArray("tb_dpwd", [
    'DPWD_MBR' => $userData['MBR_ID'],
    'DPWD_TYPE' => 2,
    'DPWD_DEVICE' => "mobile",
    'DPWD_RACC' => $account['ID_ACC'],
    'DPWD_BANKSRC' => implode("/", [$bankUser['MBANK_NAME'], $bankUser['MBANK_ACCOUNT'], $bankUser['MBANK_HOLDER']]),
    'DPWD_AMOUNT' => $amountFinal,
    'DPWD_AMOUNT_SOURCE' => $amountSource,
    'DPWD_CURR_FROM' => $account['RTYPE_CURR'],
    'DPWD_CURR_TO' => "IDR",
    'DPWD_RATE' => $convert['rate'],
    'DPWD_IP' => $helperClass->get_ip_address(),
    'DPWD_DATETIME' => date("Y-m-d H:i:s"),
]);

if(!$insert) {
    ApiResponse([
        'status'    => false,
        'message'   => "Permintaan Withdrawal gagal",
        'response'  => []
    ], 400);
}

$dpwdId = $db->insert_id;
newInsertLog([
    'mbrid' => $userData['MBR_ID'],
    'module' => "withdrawal",
    'ref' => $dpwdId,
    'device' => "mobile",
    'message' => "Withdrawal account ".$account['ACC_LOGIN'],
    'data'  => json_encode($data),
    'ip' => $helperClass->get_ip_address(),
]);

ApiResponse([
    'status'    => true,
    'message'   => "Withdrawal berhasil",
    'response'  => [
        'id'    => md5(md5($dpwdId))
    ]
]);
