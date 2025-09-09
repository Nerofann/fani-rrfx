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
    'sender-bank' => "Bank pengirim",
    'receive-bank' => "Bank Penerima",
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
$account = Account::realAccountDetail($data['account']);
if(!$account) {
    JsonResponse([
        'success' => false,
        'message' => "Akun tidak valid",
        'data' => []
    ]);
}

/** check bank pengirim */
$userBank = User::myBank($user['MBR_ID'], $data['sender-bank']);
if(!$userBank) {
    JsonResponse([
        'success' => false,
        'message' => "Bank Pengirim tidak valid",
        'data' => []
    ]);
}

/** Check Bank admin */
$adminBank = Admin::getAdminBank($data['receive-bank']);
if(!$adminBank) {
    JsonResponse([
        'success' => false,
        'message' => "Bank Penerima tidak valid",
        'data' => []
    ]);
}

/** check apakah ada deposit pending */
$isHavePending = Account::havePendingTransaction($user['MBR_ID']);
if($isHavePending) {
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
        'message' => "Jumlah deposit tidak valid",
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

/** check minimum deposit */
$minimumTopup = Helper::stringTonumber($account['RTYPE_MINTOPUP'] ?? 0);
if($jumlah < $minimumTopup && $minimumTopup != 0) {
    JsonResponse([
        'success' => false,
        'message' => "Minimum Deposit " . $account['RTYPE_CURR'] . " " . Helper::formatCurrency($minimumTopup),
        'data' => []
    ]);
}

/** check maximum deposit */
$maximumTopup = Helper::stringTonumber($account['RTYPE_MAXTOPUP'] ?? 0);
if($jumlah > $maximumTopup && $maximumTopup != 0) {
    JsonResponse([
        'success' => false,
        'message' => "Maximum Deposit " . $account['RTYPE_CURR'] . " " . Helper::formatCurrency($maximumTopup),
        'data' => []
    ]);
}

$fromCurrency = $account['RTYPE_CURR']; 
$convert = Account::accountConvertation([
    'account_id' => $account['ID_ACC'],
    'amount' => $jumlah,
    'from' => $fromCurrency,
    'to' => "USD"
]);

if(!is_array($convert)) {
    JsonResponse([
        'success' => false,
        'message' => $convert ?? "Invalid rate",
        'data' => []
    ]);
}

/** check Image */
if(empty($_FILES['image']) || $_FILES['image']['error'] != 0) {
    JsonResponse([
        'success' => false,
        'message' => "Mohon upload bukti transfer",
        'data' => []
    ]);
}

$uploadImage = FileUpload::upload_myfile($_FILES['image']);
if(!is_array($uploadImage) || !array_key_exists("filename", $uploadImage)) {
    JsonResponse([
        'success' => false,
        'message' => $uploadImage ?? "Upload bukti transfer gagal",
        'data' => []
    ]);
}

/** Insert Deposit */
$dpwdAmount = ($jumlah / $convert['rate']) ?? 0;
$insert = Database::insert("tb_dpwd", [
    'DPWD_MBR' => $user['MBR_ID'],
    'DPWD_TYPE' => 1,
    'DPWD_RACC' => $account['ID_ACC'],
    'DPWD_DEVICE' => "Web",
    'DPWD_BANKSRC' => implode("/", [$userBank['MBANK_NAME'], $userBank['MBANK_HOLDER'], $userBank['MBANK_ACCOUNT']]),
    'DPWD_BANK' => implode("/", [$adminBank['BKADM_NAME'], $adminBank['BKADM_HOLDER'], $adminBank['BKADM_ACCOUNT']]),
    'DPWD_AMOUNT' => $dpwdAmount,
    'DPWD_AMOUNT_SOURCE' => $jumlah,
    'DPWD_CURR_FROM' => $fromCurrency,
    'DPWD_CURR_TO' => "USD",
    'DPWD_RATE' => $convert['rate'],
    'DPWD_PIC' => $uploadImage['filename'],
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
