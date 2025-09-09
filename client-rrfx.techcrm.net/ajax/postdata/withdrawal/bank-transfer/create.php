<?php

use App\Models\Account;
use App\Models\Admin;
use App\Models\FileUpload;
use App\Models\Helper;
use App\Models\User;
use App\PaymentSystem\BankTransfer;
use Config\Core\Database;

$data = Helper::getSafeInput($_POST);
$required = [
    'account' => "Akun",
    'user-bank' => "Bank pengirim",
    'amount' => "Jumlah",
];

foreach($required as $req => $text) {
    if(empty($data[ $req ])) {
        JsonResponse([
            'success' => false,
            'message' => "Kolom {$text} wajib diisi",
            'data' => []
        ]);
    }
}

/** Check Account */
$account = Account::realAccountDetail_byLogin($data['account']);
if(!$account || $account['ACC_MBR'] != $user['MBR_ID']) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Account",
        'data' => []
    ]);
}

/** check user bank */
$userBank = User::myBank($user['MBR_ID'], $data['user-bank']);
if(!$userBank) {
    JsonResponse([
        'success' => false,
        'message' => "Bank tidak valid",
        'data' => []
    ]);
}

/** check apakah ada withdrawal pending */
$isHavePending = Account::havePendingTransaction($user['MBR_ID'], [2]);
if(!$isHavePending) {
    JsonResponse([
        'success' => false,
        'message' => "Masih ada transaksi yang belum selesai",
        'data' => []
    ]);
}

/** Check Jumlah */
$jumlah = Helper::stringTonumber($data['amount']);
if($jumlah <= 0) {
    JsonResponse([
        'success' => false,
        'message' => "Jumlah withdrawal tidak valid",
        'data' => []
    ]);
}

/** check Balance */
$balance = Account::marginBalance($account['ACC_LOGIN']);
if(!$balance || $balance < $jumlah) {
    JsonResponse([
        'success' => false,
        'message' => "Insufficient Balance",
        'data' => []
    ]);
}

/** check metode pembayaran */
$payment = BankTransfer::detail();
if(!$payment) {
    JsonResponse([
        'success' => false,
        'message' => "Metode pembayaran tidak tersedia",
        'data' => []
    ]);
}

$toCurrency = $account['RTYPE_CURR']; 
$convert = Account::accountConvertation([
    'account_id' => $account['ID_ACC'],
    'amount' => $jumlah,
    'from' => "USD",
    'to' => $toCurrency
]);

if(!is_array($convert)) {
    JsonResponse([
        'success' => false,
        'message' => $convert ?? "Invalid rate",
        'data' => []
    ]);
}

/** final amount */
$dpwdAmount = ($jumlah * $convert['rate']) ?? 0;

/** check minimum withdrawal */
$minimumWithdrawal = Helper::stringTonumber($account['RTYPE_MINWITHDRAWAL'] ?? 0);
if($dpwdAmount < $minimumWithdrawal && $minimumWithdrawal != 0) {
    JsonResponse([
        'success' => false,
        'message' => "Minimum Withdrawal " . $account['RTYPE_CURR'] . " " . Helper::formatCurrency($minimumWithdrawal),
        'data' => []
    ]);
}

/** check maximum withdrawal */
$maximumWithdrawal = Helper::stringTonumber($account['RTYPE_MAXWITHDRAWAL'] ?? 0);
if($dpwdAmount > $maximumWithdrawal && $maximumWithdrawal != 0) {
    JsonResponse([
        'success' => false,
        'message' => "Maximum Withdrawal " . $account['RTYPE_CURR'] . " " . Helper::formatCurrency($maximumWithdrawal),
        'data' => []
    ]);
}

/** Insert Withdrawal */
$insert = Database::insert("tb_dpwd", [
    'DPWD_MBR' => $user['MBR_ID'],
    'DPWD_TYPE' => 2,
    'DPWD_RACC' => $account['ID_ACC'],
    'DPWD_DEVICE' => "Web",
    'DPWD_BANKSRC' => implode("/", [$userBank['MBANK_NAME'], $userBank['MBANK_HOLDER'], $userBank['MBANK_ACCOUNT']]),
    'DPWD_AMOUNT' => $dpwdAmount,
    'DPWD_AMOUNT_SOURCE' => $jumlah,
    'DPWD_CURR_FROM' => "USD",
    'DPWD_CURR_TO' => $toCurrency,
    'DPWD_RATE' => $convert['rate'],
    'DPWD_IP' => Helper::get_ip_address(),
    'DPWD_DATETIME' => date("Y-m-d H:i:s"),
]);

if(!$insert) {
    JsonResponse([
        'success' => false,
        'message' => "Gagal",
        'data' => []
    ]);
}

JsonResponse([
    'success' => true,
    'message' => "Berhasil",
    'data' => []
]);
