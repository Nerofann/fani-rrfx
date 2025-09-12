<?php

use App\Models\Account;
use App\Models\Helper;
use App\Models\User;
use Config\Core\Database;

$data = Helper::getSafeInput($_POST);
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
$amountSource = Helper::stringTonumber($data['amount']);
if($amountSource <= 0) {
    ApiResponse([
        'status'    => false,
        'message'   => "Jumlah deposit tidak valid",
        'response'  => []
    ], 400);
}

/** Validasi bank user */
$bankUser = User::myBank($user['MBR_ID'], $data['bank_user']);
if(empty($bankUser)) {
    ApiResponse([
        'status'    => false,
        'message'   => "Bank User tidak valid / ditemukan",
        'response'  => []
    ], 400);
}

/** Check account */
$account = Account::realAccountDetail_byLogin($data['account']);
if(empty($account) || $account['ACC_MBR'] != $user['MBR_ID']) {
    ApiResponse([
        'status'    => false,
        'message'   => "Real Account tidak valid / ditemukan",
        'response'  => []
    ], 400);
}

/** Check ACC_MBR */
if($account['ACC_MBR'] != $user['MBR_ID']) {
    ApiResponse([
        'status'    => false,
        'message'   => "Permintaan ditolak",
        'response'  => []
    ], 400);
}

/** cek Withdrawal Pending */
if(Account::havePendingTransaction($user['MBR_ID'], [2]) !== FALSE) {
    ApiResponse([
        'status'    => false,
        'message'   => "Masih ada transaksi withdrawal dengan status pending",
        'response'  => []
    ], 400);
}

/** conversation */
$convert = Account::accountConvertation([
    'account_id' => $account['ID_ACC'],
    'amount' => $amountSource,
    'from' => "USD",
    'to' => $account['RTYPE_CURR']
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

/** Check Minimum Withdrawal */
if($amountFinal < $account['RTYPE_MINWITHDRAWAL'] && $account['RTYPE_MINWITHDRAWAL'] != 0) {
    ApiResponse([
        'status'    => false,
        'message'   => "Minimum Withdrawal ".$account['RTYPE_CURR']." ".Helper::formatCurrency($account['RTYPE_MINWITHDRAWAL']),
        'response'  => []
    ], 400);
}

/** Check Maximum Withdrawal */
if($amountFinal > $account['RTYPE_MAXWITHDRAWAL'] && $account['RTYPE_MINWITHDRAWAL'] != 0) {
    ApiResponse([
        'status'    => false,
        'message'   => "Maximum Withdrawal ".$account['RTYPE_CURR']." ".Helper::formatCurrency($account['RTYPE_MAXWITHDRAWAL']) . " ",
        'response'  => []
    ], 400);
}


/** Insert DPWD */
$insert = Database::insert("tb_dpwd", [
    'DPWD_MBR' => $user['MBR_ID'],
    'DPWD_TYPE' => 2,
    'DPWD_DEVICE' => "mobile",
    'DPWD_RACC' => $account['ID_ACC'],
    'DPWD_BANKSRC' => implode("/", [$bankUser['MBANK_NAME'], $bankUser['MBANK_ACCOUNT'], $bankUser['MBANK_HOLDER']]),
    'DPWD_AMOUNT' => $amountFinal,
    'DPWD_AMOUNT_SOURCE' => $amountSource,
    'DPWD_CURR_FROM' => "USD",
    'DPWD_CURR_TO' => $account['RTYPE_CURR'],
    'DPWD_RATE' => $convert['rate'],
    'DPWD_IP' => Helper::get_ip_address(),
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
ApiResponse([
    'status'    => true,
    'message'   => "Withdrawal berhasil",
    'response'  => [
        'id'    => md5(md5($dpwdId))
    ]
]);
