<?php

use App\Models\BankList;
use App\Models\FileUpload;
use App\Models\Helper;
use App\Models\MemberBank;
use App\Models\User;
use Config\Core\Database;
use Config\Core\EmailSender;

$data = Helper::getSafeInput($_POST);
$required = [
    'bank-name' => "Nama Bank",
    'bank-number' => "Nomor Rekening",
];

foreach($required as $req => $text) {
    if(empty($data[ $req ])) {
        ApiResponse([
            'status' => false,
            'message' => "Kolom {$text} harus diisi",
            'response' => []
        ], 400);
    }
}

/** Check Max Bank */
$banks = User::myBank($user['MBR_ID']);
if(count($banks) >= 2) {
    ApiResponse([
        'status' => false,
        'message' => "Mencapai limit pembuatan bank",
        'response' => []
    ], 400);
}

/** check Nomor rekening */
$rekening = Helper::stringTonumber($data['bank-number'] ?? 0);
if(is_numeric($rekening) === FALSE || $rekening <= 0) {
    ApiResponse([
        'status' => false,
        'message' => "Nomor rekening tidak valid",
        'data' => []
    ], 400);
}

if(strlen(strval($rekening)) < 10) {
    ApiResponse([
        'status' => false,
        'message' => "Nomor rekening harus lebih dari 9 digit",
        'data' => []
    ], 400);
}

$sqlCheck = $db->query("SELECT ID_MBANK FROM tb_member_bank WHERE MBANK_MBR = ".$user['MBR_ID']." AND MBANK_ACCOUNT = {$rekening} LIMIT 1");
if($sqlCheck->num_rows != 0) {
    ApiResponse([
        'status' => false,
        'message' => "Nomor Rekening sudah terdaftar",
        'response' => []
    ], 400);
}

/** Check Nama Bank */
$bankName = BankList::findByName($data['bank-name']);
if(!$bankName) {
    ApiResponse([
        'status' => false,
        'message' => "Nama Bank tidak valid",
        'response' => []
    ], 400);
}

if(empty($_FILES['bank-image']) || $_FILES['bank-image']['error'] != 0) {
    ApiResponse([
        'status' => false,
        'message' => "Mohon upload foto cover bank",
        'response' => []
    ], 400);
}

$uploadCoverBank = FileUpload::upload_myfile($_FILES['bank-image'], "bank_cover");
if(!is_array($uploadCoverBank) || !array_key_exists("filename", $uploadCoverBank)) {
    ApiResponse([
        'status' => false,
        'message' => $uploadCoverBank ?? "Gagal mengunggah file cover bank",
        'response' => []
    ], 400);
}

/** insert */
$otpCode = random_int(1000, 9999);
$otpExpired = date("Y-m-d H:i:s", strtotime("+30 minute"));
$insert = Database::insert("tb_member_bank", [
    'MBANK_MBR' => $user['MBR_ID'],
    'MBANK_HOLDER' => $user['MBR_NAME'],
    'MBANK_NAME' => $data['bank-name'],
    'MBANK_ACCOUNT' => $rekening,
    'MBANK_OTP' => $otpCode,
    'MBANK_OTP_EXPIRED' => $otpExpired,
    'MBANK_STS' => MemberBank::$statusNotVerified,
    'MBANK_IMG' => $uploadCoverBank['filename'],
    'MBANK_DATETIME' => date("Y-m-d H:i:s")
]);

/** Email OTP */
$emailData = [
    'subject' => "Bank OTP Verification",
    'otp'  => $otpCode,
];

$emailSender = EmailSender::init(['email' => $user['MBR_EMAIL'], 'name' => $user['MBR_NAME']]);
$emailSender->useFile("otp", $emailData);
$send = $emailSender->send();

if(!$insert) {
    ApiResponse([
        'status' => false,
        'message' => "Gagal membuat bank",
        'response' => []
    ], 400);
}

ApiResponse([
    'status' => true,
    'message' => "Berhasil membuat bank",
    'response' => []
]);