<?php

use App\Models\Country;
use App\Models\Helper;
use Config\Core\Database;
use App\Models\Logger;
use App\Models\User;
use Allmedia\Shared\Verihubs\Verihubs;
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
$required = ['fullname', 'email', 'password', 'phone_code', 'phone', 'otp', 'country'];
foreach($required as $req) {
    if(empty($data[ $req ])) {
        JsonResponse([
            'success'   => false,
            'message'   => "{$req} field is required",
            'data'      => []
        ]);
    }
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

/** Validasi OTP database */
$phone = $verihub->phoneValidation($data['phone_code'], $data['phone']);
if(!$phone) {
    JsonResponse([
        'success'   => false,
        'message'   => "Invalid Phone Number",
        'data'      => []
    ]);
}

// $isValidOTP = $verihub->validate_otp_sms($phone, $data['otp']);
// if($isValidOTP !== TRUE) {
//     JsonResponse([
//         'success'   => false,
//         'message'   => $isValidOTP ?? "Invalid otp",
//         'data'      => []
//     ]);
// }

/** Insert */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($db);

$newMbrId = User::createMbrId();
$passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);
$dateExpired = date("Y-m-d H:i:s", strtotime("+1 hour"));
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
    'MBR_OTP' => $data['otp'],
    'MBR_OTP_EXPIRED' => $dateExpired,
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

// /** Validasi OTP dengan Verihubs */
// $otpVerification = $verihub->sendOtp_sms_verification(['otp' => $data['otp'], 'phone' => $phone]);
// if(!$otpVerification['success']) {
//     $db->rollback();
//     JsonResponse([
//         'success'   => false,
//         'message'   =>  $otpVerification['message'],
//         'data'      => []
//     ]);
// }

/** Email Verifikasi */
$emailData = [
    'subject'   => "Email Verification",
    'code'  => md5(md5($newMbrId.$data['otp'])),
];

$emailSender = EmailSender::init(['email' => $data['email'], 'name' => $data['fullname']]);
$emailSender->useFile("register", $emailData);
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