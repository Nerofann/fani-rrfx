<?php

use App\Models\Helper;
use App\Models\User;
use App\Models\Account;
use App\Models\ProfilePerusahaan;
use Config\Core\Database;
use Config\Core\EmailSender;

$GETACC = Account::myAccount($user['MBR_ID']);
$GETBNK = User::myBank($user['MBR_ID']);
$accounts = (count($GETACC) > 0) ? implode(', ', array_map(function($ARR){ return $ARR["ACC_LOGIN"]; }, $GETACC)) : '';
$rmnfreem = (count($GETACC) > 0) ? implode(', ', array_map(function($ARR){ return $ARR["MARGIN_FREE"]; }, $GETACC)) : '';
$usrbanks = (count($GETBNK) > 0) ? implode(', ', array_map(function($ARR){ return $ARR["MBANK_ACCOUNT"]; }, $GETBNK)) : '';

$data = Helper::getSafeInput($_POST);
$required = [
    'otp-code' => "Kode OTP",
];

$otp = $data['otp-code'];


foreach($required as $req => $text) {
    if(empty($data[ $req ])) {
        JsonResponse([
            'success' => false,
            'message' => "Kolom {$text} harus diisi",
            'data' => []
        ]);
    }
}


/** check expired */
if(empty($user['MBR_OTP_EXPIRED']) || strtotime($user['MBR_OTP_EXPIRED']) < strtotime("now")) {
    JsonResponse([
        'success' => false,
        'message' => "Kode OTP kadaluarsa",
        'data' => []
    ]);
}


$STORED_DATA = [
    'DLTACC_MBR'        => $user['MBR_ID'],
    'DLTACC_NAMLENG'    => $user['MBR_NAME']
];
$CHCK_VAR = [
    'DLTACC_ACCOUNT'    => $accounts,
    'DLTACC_NOREK_NSBH' => $usrbanks,
    'DLTACC_NOIDT'      => $user["MBR_NO_IDT"],
    'DLTACC_EMAIL'      => $user["MBR_EMAIL"],
    'DLTACC_NOTELP'     => $user["MBR_PHONE"],
    'DLTACC_LST_EQT'    => $rmnfreem
];
foreach ($CHCK_VAR as $key => $value) {
    if(!empty($value)){
        $STORED_DATA["$key"] = $value;
    }
}
$insert = Database::insert("tb_dlt_account", $STORED_DATA);
if(!$insert) {
    JsonResponse([
        'success' => false,
        'message' => "Gagal insert data",
        'data' => []
    ]);
}

/** Email Notification for admin*/
$emailData = [
    'subject' => "Penghapusan User ".date("d/m/Y"),
    'nama'    => $user['MBR_NAME'],
    'email'   => $user['MBR_EMAIL']
];


$emailSender = EmailSender::init(['email' => ProfilePerusahaan::$emailDealing, 'name' => ProfilePerusahaan::$namaDealing]);
$emailSender->useFile("otp-delete-admin-notif", $emailData);
$send = $emailSender->send();

JsonResponse([
    'success' => true,
    'message' => "Berhasil mengkonfirmasi, silahkan tunggu persetujuan dari admin.",
    'data' => []
]);