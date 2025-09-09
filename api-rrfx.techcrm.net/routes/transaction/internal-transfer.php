<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;
use App\Models\Logger;
use Config\Core\Database;
use Config\Core\EmailSender;

$apiManager = MetatraderFactory::apiManager();
$data = Helper::getSafeInput($_POST);
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
$amount = Helper::stringTonumber($data['amount']);
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

/** check akun pengirim */
$fromAccount = Account::realAccountDetail_byLogin($loginPengirim);
if(!$fromAccount) {
    ApiResponse([
        'status'    => false,
        'message'   => "Akun Pengirim tidak valid",
        'response'  => []
    ], 400);
}

/** check akun penerima */
$toAccount = Account::realAccountDetail_byLogin($loginPenerima);
if(!$toAccount) {
    ApiResponse([
        'status'    => false,
        'message'   => "Akun Penerima tidak valid",
        'response'  => []
    ], 400);
}

/** Check rate account */
if($fromAccount['RTYPE_RATE'] != $toAccount['RTYPE_RATE']) {
    ApiResponse([
        'status'    => false,
        'message'   => "Gagal, Invalid Rate",
        'response'  => []
    ], 400);
}

/** Check Balance Pengirim */
$balancePengirim = Account::marginBalance($fromAccount['ACC_LOGIN']);
if($balancePengirim < $amount) {
    ApiResponse([
        'status'    => false,
        'message'   => "Insufficient balance metatrader",
        'response'  => []
    ], 400);
}

/** Start Transaction */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($db);

/** Insert */
$code = uniqid();
$statusDeposit = false;
$apiManager = MetatraderFactory::apiManager();
$insert = Database::insert("tb_internal_transfer", [
    'IT_CODE' => $code,
    'IT_FROM' => $fromAccount['ACC_LOGIN'],
    'IT_TO' => $toAccount['ACC_LOGIN'],
    'IT_AMOUNT' => $amount,
    'IT_AMOUNT_SOURCE' => $amount,
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
    ApiResponse([
        'status'    => false,
        'message'   => "Invalid Status",
        'response'  => []
    ], 400);
}

$idInternalTransfer = $db->insert_id;

/** Withdrawal dari akun pengirim */
$withdrawal = $apiManager->deposit([
    'login' => $fromAccount['ACC_LOGIN'],
    'amount' => ($amount * -1),
    'comment' => "IT-{$code}"
]);

if(is_object($withdrawal) === FALSE || !property_exists($withdrawal, "ticket")) {
    $db->rollback();
    ApiResponse([
        'status' => false,
        'message' => "Invalid Status balance " . $fromAccount['ACC_LOGIN'],
        'response' => []
    ]);
}

/** update ticket withdrawal */
Database::update("tb_internal_transfer", ['IT_TICKET_FROM' => $withdrawal->ticket], ['IT_CODE' => $code]);

/** Deposit ke akun penerima */
$deposit = $apiManager->deposit([
    'login' => $toAccount['ACC_LOGIN'],
    'amount' => $amount,
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
    'message' => "Internal Transfer from " . $loginPengirim . " to " . $loginPenerima . " $amount USD",
    'data' => $data 
]);

switch($statusDeposit) {
    case (true):
        $emailData = [
            'subject' => "Internal Transfer Successfull",
            'accountFrom' => $fromAccount['ACC_LOGIN'],
            'accountTo' => $toAccount['ACC_LOGIN'],
            'amount' => "$".Helper::formatCurrency($amount)
        ];
        
        $emailSender = EmailSender::init(['email' => $user['MBR_EMAIL'], 'name' => $user['MBR_NAME']]);
        $emailSender->useFile("internal-transfer-success", $emailData);
        $send = $emailSender->send();
        break;

    case (false):
        $emailData = [
            'subject' => "Internal Transfer Failed",
            'accountFrom' => $fromAccount['ACC_LOGIN'],
            'accountTo' => $toAccount['ACC_LOGIN'],
            'amount' => "$".Helper::formatCurrency($amount)
        ];
        
        $emailSender = EmailSender::init(['email' => $user['MBR_EMAIL'], 'name' => $user['MBR_NAME']]);
        $emailSender->useFile("internal-transfer-failed", $emailData);
        $send = $emailSender->send();
        break;
}


$db->commit();
ApiResponse([
    'status'    => true,
    'message'   => "Transfer Berhasil",
    'response'  => [
        'id'    => md5(md5($idInternalTransfer))
    ]
]);