<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;
use App\Models\Logger;
use App\Models\ProfilePerusahaan;
use Config\Core\Database;
use Config\Core\EmailSender;

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
$jumlah = Helper::stringTonumber($data['amount']);
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

/** Check Balance Pengirim */
$balancePengirim = Account::marginBalance($fromAccount['ACC_LOGIN']);
if($balancePengirim < $jumlah) {
    JsonResponse([
        'success' => false,
        'message' => "Insufficient balance metatrader",
        'data' => []
    ]);
}

/** Insert */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($db);

$code = uniqid();
$statusDeposit = false;
$apiManager = MetatraderFactory::apiManager();
$insert = Database::insert("tb_internal_transfer", [
    'IT_CODE' => $code,
    'IT_FROM' => $fromAccount['ACC_LOGIN'],
    'IT_TO' => $toAccount['ACC_LOGIN'],
    'IT_AMOUNT' => $jumlah,
    'IT_AMOUNT_SOURCE' => $jumlah,
    'IT_CURR_TO' => $toAccount['RTYPE_CURR'],
    'IT_CURR_FROM' => $fromAccount['RTYPE_CURR'],
    'IT_RATE_TO' => 1,
    'IT_RATE_FROM' => 1,
    // '' => ,
    // '' => $deposit->ticket,
    'IT_DATETIME' => date("Y-m-d H:i:s"),
]);

if(!$insert) {
    $db->rollback();
    JsonResponse([
        'success' => false,
        'message' => "Invalid Status Save",
        'data' => []
    ]);
}

/** Withdrawal dari akun pengirim */
$withdrawal = $apiManager->deposit([
    'login' => $fromAccount['ACC_LOGIN'],
    'amount' => ($jumlah * -1),
    'comment' => "IT-{$code}"
]);

if(is_object($withdrawal) === FALSE || !property_exists($withdrawal, "ticket")) {
    $db->rollback();
    JsonResponse([
        'success' => false,
        'message' => "Invalid Status balance " . $fromAccount['ACC_LOGIN'],
        'data' => []
    ]);
}

/** update ticket withdrawal */
Database::update("tb_internal_transfer", ['IT_TICKET_FROM' => $withdrawal->ticket], ['IT_CODE' => $code]);

/** Deposit ke akun penerima */
$deposit = $apiManager->deposit([
    'login' => $toAccount['ACC_LOGIN'],
    'amount' => $jumlah,
    'comment' => "IT-{$code}"
]);

if(is_object($deposit) !== FALSE && property_exists($deposit, "ticket")) {
    /** update ticket deposit */
    Database::update("tb_internal_transfer", ['IT_TICKET_TO' => $deposit->ticket], ['IT_CODE' => $code]);
    $statusDeposit = true;
}

Logger::client_log([
    'mbrid' => $user['MBR_ID'],
    'module' => "internal-transfer",
    'message' => "Internal Transfer from " . $data['from-account'] . " to " . $data['to-account'] . " $jumlah USD",
    'data' => $data 
]);

switch($statusDeposit) {
    case (true):
        $emailData = [
            'subject' => "Internal Transfer Successfull",
            'accountFrom' => $fromAccount['ACC_LOGIN'],
            'accountTo' => $toAccount['ACC_LOGIN'],
            'amount' => "$".Helper::formatCurrency($jumlah)
        ];
        
        $emailSender = EmailSender::init(['email' => $user['MBR_EMAIL'], 'name' => $user['MBR_NAME']]);
        $emailSender->useFile("internal-transfer-success", $emailData);
        $emailSender->addBcc(ProfilePerusahaan::$emailDealing , ProfilePerusahaan::$namaDealing);
        $send = $emailSender->send();
        break;

    case (false):
        $emailData = [
            'subject' => "Internal Transfer Failed",
            'accountFrom' => $fromAccount['ACC_LOGIN'],
            'accountTo' => $toAccount['ACC_LOGIN'],
            'amount' => "$".Helper::formatCurrency($jumlah)
        ];
        
        $emailSender = EmailSender::init(['email' => $user['MBR_EMAIL'], 'name' => $user['MBR_NAME']]);
        $emailSender->useFile("internal-transfer-failed", $emailData);
        $emailSender->addBcc(ProfilePerusahaan::$emailDealing , ProfilePerusahaan::$namaDealing);
        $send = $emailSender->send();
        break;
}

$db->commit();
JsonResponse([
    'success' => true,
    'message' => "Berhasil",
    'data' => []
]);