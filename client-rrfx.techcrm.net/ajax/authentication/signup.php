<?php

use App\Models\Country;
use App\Models\Helper;
use Config\Core\Database;
use App\Models\Logger;
use App\Models\User;
use App\Factory\VerihubFactory;
use Config\Core\EmailSender;

$verihub = VerihubFactory::init();
$data = Helper::getSafeInput($_POST);
$defaultIdspn = 1000000000;

if(empty($data['terms'])) {
    JsonResponse([
        'success'   => false,
        'message'   => "Mohon menyetujui telah mambaca dan memahami Syarat, ketentuan serta Kebijakan Privasi",
        'data'      => []
    ]);
}

$data['country'] = "Indonesia";
$required = ['fullname', 'email', 'password', 'phone_code', 'phone', 'country'];
foreach($required as $req) {
    if(empty($data[ $req ])) {
        JsonResponse([
            'success'   => false,
            'message'   => "{$req} field is required",
            'data'      => []
        ]);
    }
}

/** validasi nama lengkap */
if(!preg_match("/^[a-zA-Z\s]+$/", $data['fullname'])) {
    JsonResponse([
        'success' => false,
        'message' => "Nama Lengkap tidak valid, tidak boleh mengandung symbol dan angka",
        'data' => []
    ]);
}

/** Check email */
$sqlCheckEmail = $db->query("SELECT * FROM tb_member WHERE LOWER(MBR_EMAIL) = LOWER('".$data['email']."') LIMIT 1");
if($sqlCheckEmail->num_rows != 0) {
    JsonResponse([
        'success'   => false,
        'message'   => "Email already registered",
        'data'      => []
    ]);
} 


/** Validation Password */
$validationPassword = User::validation_password($data['password']);
if($validationPassword !== TRUE) {
    JsonResponse([
        'success'   => false,
        'message'   => $validationPassword,
        'data'      => []
    ]);
}

/** Check Country */
$countries = Country::countries();
$checkCountry = array_search($data['country'], array_column($countries, "COUNTRY_NAME"));
if($checkCountry === FALSE) {
    JsonResponse([
        'success'   => false,
        'message'   => "Invalid Country",
        'data'      => []
    ]);
}

/** refferal code */
if(!empty($data['refferal'])) {
    $defaultIdspn = 0;
    $sqlCheck = $db->query("SELECT MBR_ID FROM tb_member WHERE LOWER(MBR_USER) = LOWER('".$data['refferal']."') LIMIT 1");
    if($sqlCheck->num_rows == 0) {
        JsonResponse([
            'success'   => false,
            'message'   => "Referral code tidak valid",
            'data'      => []
        ]);
    }

    $defaultIdspn = $sqlCheck->fetch_assoc()['MBR_ID'] ?? 0;
}

if($defaultIdspn == 0) {
    JsonResponse([
        'success'   => false,
        'message'   => "Invalid Refferal Code",
        'data'      => []
    ]);
}

/** Validasi Nomor Telepon */
$phone = $verihub->phoneValidation($data['phone_code'], $data['phone']);
if(!$phone) {
    JsonResponse([
        'success'   => false,
        'message'   => "Invalid Phone Number",
        'data'      => []
    ]);
}

/** Check pajang nomor telepon */
$phoneLength = strlen(str_replace($data['phone_code'], "", $phone) ?? 0);
if($phoneLength < 10 || $phoneLength > 13) {
    JsonResponse([
        'success'   => false,
        'message'   => "Nomor telepon harus lebih dari 9 digit dan kurang dari 14 digit",
        'data'      => []
    ]);
}

/** Check nomor telepon sudah terdaftar / belum */
$sqlCheckPhone = $db->query("SELECT ID_MBR FROM tb_member WHERE MBR_PHONE = '{$phone}' LIMIT 1");
if($sqlCheckPhone->num_rows != 0) {
    JsonResponse([
        'success' => false,
        'message' => "Nomor Telepon telah terdaftar",
        'data' => []
    ]);
}

/** Insert */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($db);

$newMbrId = User::createMbrId();
$passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
$otpCode = random_int(1000, 9999);
$otpExpired = date("Y-m-d H:i:s", strtotime("+5 minute"));

/** insert tb_member */
$insert = Database::insert("tb_member", [
    'MBR_ID' => $newMbrId,
    'MBR_IDSPN' => $defaultIdspn,
    'MBR_CODE' => uniqid(),
    'MBR_EMAIL' => $data['email'],
    'MBR_PHONE_CODE' => $data['phone_code'],
    'MBR_PHONE' => $phone,
    'MBR_PASS' => $passwordHash,
    'MBR_NAME' => $data['fullname'],
    'MBR_COUNTRY' => $data['country'],
    'MBR_OTP' => $otpCode,
    'MBR_OTP_EXPIRED' => $otpExpired,
    'MBR_STS' => 0
]);

if(!$insert) {
    $db->rollback();
    JsonResponse([
        'success'   => false,
        'message'   => "Registration failed",
        'data'      => []
    ]);
}

/** Email OTP */
$emailData = [
    'subject' => "OTP Verification",
    'otp'  => $otpCode,
];

$emailSender = EmailSender::init(['email' => $data['email'], 'name' => $data['fullname']]);
$emailSender->useFile("otp", $emailData);
$send = $emailSender->send();

/** Log */
Logger::client_log([
    'mbrid' => $newMbrId,
    'module' => "signup",
    'data' => $data,
    'message' => "Pendaftaran user baru " . $data['email']
]);

$db->commit();
JsonResponse([
    'success'   => true,
    'message'   => "Registrasi berhasil, Email verifikasi telah dikirimkan ke " . $data['email'],
    'data'      => [
        'redirect'  => "/"
    ]
]);