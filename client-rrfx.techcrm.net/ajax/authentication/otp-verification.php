<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;
use App\Models\Logger;
use App\Models\ProfilePerusahaan;
use App\Models\Token;
use App\Models\User;
use Config\Core\Database;
use Config\Core\EmailSender;

$data = Helper::getSafeInput($_POST);
$required = ['otp', 'code'];
foreach($required as $req) {
    if(empty($data[ $req ])) {
        JsonResponse([
            'success' => false,
            'message' => "Invalid Message",
            'data' => []
        ]);
    }
}

/** check code */
$uniqueCode = $data['code'];
$sqlGet = $db->query("SELECT * FROM tb_member WHERE MD5(MD5(CONCAT(MBR_ID, ID_MBR))) = '$uniqueCode' AND MBR_STS = 0 LIMIT 1");
if($sqlGet->num_rows != 1) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Code",
        'data' => []
    ]);
}

/** check otp */
$user = $sqlGet->fetch_assoc();
if($user['MBR_OTP'] != $data['otp']) {
    JsonResponse([
        'success' => false,
        'message' => "Kode Otp Salah",
        'data' => []
    ]);
}

/** check expired */
if(empty($user['MBR_OTP_EXPIRED']) || strtotime($user['MBR_OTP_EXPIRED']) < strtotime("now")) {
    JsonResponse([
        'success' => false,
        'message' => "Kode OTP kadaluarsa",
        'data' => []
    ]);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($db);

/** Update Sts */
$update = Database::update("tb_member", ['MBR_STS' => 2, 'MBR_OTP_EXPIRED' => date("Y-m-d H:i:s")], ['MBR_ID' => $user['MBR_ID']]);
if(!$update) {
    $db->rollback();
    JsonResponse([
        'success' => false,
        'message' => "Verification failed",
        'data' => []
    ]);
}

/** Generate Token */
$accessToken = Token::generateAccessToken($user['MBR_ID']);
$refreshToken = Token::generateRefreshToken($user['MBR_ID']);

/** Save Token */
$saveToken = Token::saveTokens($user['MBR_ID'], $accessToken, $refreshToken);
if(!$saveToken) {
    $db->rollback();
    JsonResponse([
        'success'   => false,
        'message'   => "Invalid Token",
        'data'      => []
    ]);
}

/** Set Token to Cookie */
User::setAuthData([
    'access_token' => $accessToken,
    'refresh_token' => $refreshToken
]);

/** create demo account */
$demoAccount = Account::getDemoAccount(md5(md5($user['MBR_ID'])));
if(empty($demoAccount)) {
    $createDemo = MetatraderFactory::createDemo($user['MBR_NAME'], $user['MBR_EMAIL']);
    if(!$createDemo['success']) {
        $db->rollback();
        JsonResponse([
            'success'   => false,
            'message'   => $createDemo['message'] ?? "Gagal",
            'data'      => []
        ]);
    }
    
    /** Insert Demo */
    $demoData = $createDemo['data'];
    $insertDemo = Database::insert("tb_racc", [
        'ACC_MBR' => $user['MBR_ID'],
        'ACC_DERE' => 2,
        'ACC_TYPE' => $demoData['type'],
        'ACC_LOGIN' => $demoData['login'],
        'ACC_PASS' => $demoData['password'],
        'ACC_INVESTOR' => $demoData['investor'],
        'ACC_PASSPHONE' => $demoData['passphone'],
        'ACC_INITIALMARGIN' => MetatraderFactory::$initMarginDemo,
        'ACC_FULLNAME' => $user['MBR_NAME'],
        'ACC_DATETIME' => date("Y-m-d H:i:s"),
    ]);
    
    /** Send Notification Email */
    $emailData = [
        "subject" => "Demo Account Information - ". ProfilePerusahaan::get()['PROF_COMPANY_NAME'] ." ".date('Y-m-d H:i:s'),
        "name" => $user["MBR_NAME"],
        "login" => $demoData['login'],
        "metaPassword"  => $demoData['password'],
        "metaInvestor"  => $demoData['investor'],
        "metaPassPhone" => $demoData['passphone'],
    ];
    
    $emailSender = EmailSender::init(['email' => $user['MBR_EMAIL'], 'name' => $user['MBR_NAME']]);
    $emailSender->useFile("create-demo", $emailData);
    $send = $emailSender->send();
}

Logger::client_log([
    'mbrid' => $user['MBR_ID'],
    'module' => "otp-verification",
    'message' => "OTP Verification",
    'data' => array_merge($data, ($emailData ?? []))
]);

$db->commit();
JsonResponse([
    'success'   => true,
    'message'   => "Verifikasi OTP berhasil",
    'data'      => [
        'redirect' => "/verif/step-1"
    ]
]);