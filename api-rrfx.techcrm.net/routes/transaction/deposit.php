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
    'account' => "Real Account",
    'amount' => "Jumlah Deposit",
    'bank_user' => "Bank User",
    'bank_admin' => "Bank Admin",
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

/** Validasi bukti transfer */
if(empty($_FILES['image']) || $_FILES['image']['error'] != 0) {
    ApiResponse([
        'status'    => false,
        'message'   => "Mohon upload bukti transfer",
        'response'  => []
    ], 400);
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

/** Validasi Bank Admin */
$bankAdmin = Admin::getAdminBank($data['bank_admin']);
if(empty($bankAdmin)) {
    ApiResponse([
        'status'    => false,
        'message'   => "Bank Admin tidak valid / ditemukan",
        'response'  => []
    ], 400);
}

/** Check account */
$account = Account::realAccountDetail_byLogin($data['account']);
if(empty($account)) {
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


/** check apakah ada deposit pending */
$isHavePending = Account::havePendingTransaction($user['MBR_ID']);
if($isHavePending) {
    ApiResponse([
        'status'    => false,
        'message'   => "Masih ada transaksi yang belum selesai",
        'response'  => []
    ], 400);
}

/** check metode pembayaran */
$payment = BankTransfer::detail();
if(!$payment) {
    ApiResponse([
        'status'    => false,
        'message'   => "Metode pembayaran tidak tersedia",
        'response'  => []
    ], 400);
}

/** check minimum deposit */
$minimumTopup = Helper::stringTonumber($account['RTYPE_MINTOPUP'] ?? 0);
if($amountSource < $minimumTopup && $minimumTopup != 0) {
    ApiResponse([
        'status'    => false,
        'message'   => "Minimum Deposit " . $account['RTYPE_CURR'] . " " . Helper::formatCurrency($minimumTopup),
        'response'  => []
    ], 400);
}

/** check maximum deposit */
$maximumTopup = Helper::stringTonumber($account['RTYPE_MAXTOPUP'] ?? 0);
if($amountSource > $maximumTopup && $maximumTopup != 0) {
    ApiResponse([
        'status'    => false,
        'message'   => "Maximum Deposit " . $account['RTYPE_CURR'] . " " . Helper::formatCurrency($maximumTopup),
        'response'  => []
    ], 400);
}

/** Check Account currency === Bank Admin currency */
if($account['RTYPE_CURR'] != $bankAdmin['BKADM_CURR']) {
    ApiResponse([
        'status'    => false,
        'message'   => "Invalid Bank Currency",
        'response'  => []
    ], 400);
}

/** conversation */
$convert = Account::accountConvertation([
    'account_id' => $account['ID_ACC'],
    'amount' => $amountSource,
    'from' => $account['RTYPE_CURR'],
    'to' => "USD"
]);

if(!is_array($convert)) {
    ApiResponse([
        'status'    => false,
        'message'   => $convert,
        'response'  => []
    ], 400);
}

/** Set Amount Final */
$amountFinal = ($amountSource / $convert['rate']);

/** Upload File */
$fileUpload = FileUpload::upload_myfile($_FILES['image']);
if(!is_array($fileUpload) || !array_key_exists("filename", $fileUpload)) {
    ApiResponse([
        'status'    => false,
        'message'   => $fileUpload ?? "Invalid Upload",
        'response'  => []
    ], 400);
}

/** Insert DPWD */
$insert = Database::insert("tb_dpwd", [
    'DPWD_MBR' => $user['MBR_ID'],
    'DPWD_TYPE' => 1,
    'DPWD_DEVICE' => "mobile",
    'DPWD_RACC' => $account['ID_ACC'],
    'DPWD_BANKSRC' => implode("/", [$bankUser['MBANK_NAME'], $bankUser['MBANK_ACCOUNT'], $bankUser['MBANK_HOLDER']]),
    'DPWD_BANK' => implode("/", [$bankAdmin['BKADM_NAME'], $bankAdmin['BKADM_ACCOUNT'], $bankAdmin['BKADM_HOLDER']]),
    'DPWD_AMOUNT' => $amountFinal,
    'DPWD_AMOUNT_SOURCE' => $amountSource,
    'DPWD_CURR_FROM' => $account['RTYPE_CURR'],
    'DPWD_CURR_TO' => "IDR",
    'DPWD_RATE' => $convert['rate'],
    'DPWD_PIC' => $fileUpload['filename'],
    'DPWD_IP' => Helper::get_ip_address(),
    'DPWD_DATETIME' => date("Y-m-d H:i:s"),
]);

if(!$insert) {
    ApiResponse([
        'status'    => false,
        'message'   => "Permintaan deposit gagal",
        'response'  => []
    ], 400);
}

$dpwdId = $db->insert_id;
ApiResponse([
    'status'    => true,
    'message'   => "Deposit berhasil",
    'response'  => [
        'id'    => md5(md5($dpwdId))
    ]
]);