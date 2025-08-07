<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;

$data = Helper::getSafeInput($_POST);
$required = [
    'from-account' => "Akun Pengirim",
    'to-account' => "Akun Penerima",
    'amount' => "Jumlah"
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

/** check akun pengirim */
$fromAccount = Account::realAccountDetail_byLogin($data['from-account']);
if(!$fromAccount) {
    JsonResponse([
        'success' => false,
        'message' => "Akun Pengirim tidak valid",
        'data' => []
    ]);
}

/** check akun penerima */
$toAccount = Account::realAccountDetail_byLogin($data['to-account']);
if(!$toAccount) {
    JsonResponse([
        'success' => false,
        'message' => "Akun Penerima tidak valid",
        'data' => []
    ]);
}

/** check jumlah */
$jumlah = Helper::stringTonumber($data['jumlah']);
if(is_numeric($jumlah) === FALSE || $jumlah <= 0) {
    JsonResponse([
        'success' => false,
        'message' => "Jumlah tidak valid",
        'data' => []
    ]);
}

/** Check rate account */
if($fromAccount['RTYPE_RATE'] != $toAccount['RTYPE_RATE']) {
    JsonResponse([
        'success' => false,
        'message' => "Gagal, Rate akun tidak diperbolehkan",
        'data' => []
    ]);
}


/** Deposit ke akun penerima */
$code = uniqid();
$apiManager = MetatraderFactory::apiManager();
$depositData = [
    'login' => $fromAccount['ACC_LOGIN'],
    'amount' => $jumlah,
    'comment' => "IT-{$code}"
];

$deposit = $apiManager->deposit($depositData);
print_r($deposit);

// /** insert */
// $insert = Database::insert("tb_internal_transfer", [
//     'IT_FROM' => $fromAccount,
//     'IT_TO' => $toAccount,
//     'IT_AMOUNT' => $jumlah,
//     'IT_AMOUNT_SOURCE' => $jumlah,
//     'IT_CURR_TO' => $toAccount['RTYPE_CURR'],
//     'IT_CURR_FROM' => $fromAccount['RTYPE_CURR'],
//     'IT_RATE_TO' => 1,
//     'IT_RATE_FROM' => 1,
//     'IT_TICKET_FROM' => ,
//     'IT_TICKET_TO' => ,
//     'IT_DATETIME' => ,
// ]);