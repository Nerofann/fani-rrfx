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
$required = ['otp'];
foreach($required as $req) {
    if(empty($data[ $req ])) {
        ApiResponse([
            'status' => false,
            'message' => "Invalid Message",
            'response' => []
        ]);
    }
}

/** check token */
$userToken = $_SERVER['HTTP_AUTHORIZATION'] ?? "";
$userToken = str_replace("Bearer ", "", $userToken);
$isValid = Token::verifyToken($userToken);
if(!$isValid || !is_array($isValid)) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Token",
        'response' => []
    ], 300);
}

$userData = User::findByMemberId($isValid['user_id']);
$userId = md5(md5($isValid['user_id']));
if(empty($userData)) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid User",
        'response' => []
    ], 400);
}

/** check otp */
if($userData['MBR_OTP'] != $data['otp']) {
    ApiResponse([
        'status' => false,
        'message' => "Kode Otp Salah",
        'response' => []
    ]);
}

/** check status */
if($userData['MBR_STS'] != 0) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Status",
        'response' => []
    ]);
}

/** check expired */
if(empty($userData['MBR_OTP_EXPIRED']) || strtotime($userData['MBR_OTP_EXPIRED']) < strtotime("now")) {
    ApiResponse([
        'status' => false,
        'message' => "Kode OTP kadaluarsa",
        'response' => []
    ]);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($db);

/** Update Sts */
$update = Database::update("tb_member", ['MBR_STS' => 2, 'MBR_OTP_EXPIRED' => date("Y-m-d H:i:s")], ['MBR_ID' => $userData['MBR_ID']]);
if(!$update) {
    $db->rollback();
    ApiResponse([
        'status' => false,
        'message' => "Verification failed",
        'response' => []
    ]);
}

/** Generate Token */
$accessToken = Token::generateAccessToken($userData['MBR_ID']);
$refreshToken = Token::generateRefreshToken($userData['MBR_ID']);

/** Save Token */
$saveToken = Token::saveTokens($userData['MBR_ID'], $accessToken, $refreshToken);
if(!$saveToken) {
    $db->rollback();
    ApiResponse([
        'status'   => false,
        'message'   => "Invalid Token",
        'response'      => []
    ]);
}

/** Set Token to Cookie */
User::setAuthData([
    'access_token' => $accessToken,
    'refresh_token' => $refreshToken
]);

/** create demo account */
$demoAccount = Account::getDemoAccount(md5(md5($userData['MBR_ID'])));
if(empty($demoAccount)) {
    $createDemo = MetatraderFactory::createDemo($userData['MBR_NAME'], $userData['MBR_EMAIL']);
    if(!$createDemo['success']) {
        $db->rollback();
        ApiResponse([
            'status'   => false,
            'message'   => $createDemo['message'] ?? "Gagal",
            'response'      => []
        ]);
    }
    
    /** Insert Demo */
    $demoData = $createDemo['data'];
    $insertDemo = Database::insert("tb_racc", [
        'ACC_MBR' => $userData['MBR_ID'],
        'ACC_DERE' => 2,
        'ACC_TYPE' => $demoData['type'],
        'ACC_LOGIN' => $demoData['login'],
        'ACC_PASS' => $demoData['password'],
        'ACC_INVESTOR' => $demoData['investor'],
        'ACC_PASSPHONE' => $demoData['passphone'],
        'ACC_INITIALMARGIN' => MetatraderFactory::$initMarginDemo,
        'ACC_FULLNAME' => $userData['MBR_NAME'],
        'ACC_DATETIME' => date("Y-m-d H:i:s"),
    ]);
    
    /** Send Notification Email */
    $emailData = [
        "subject" => "Demo Account Information - ". ProfilePerusahaan::get()['PROF_COMPANY_NAME'] ." ".date('Y-m-d H:i:s'),
        "name" => $userData["MBR_NAME"],
        "login" => $demoData['login'],
        "metaPassword"  => $demoData['password'],
        "metaInvestor"  => $demoData['investor'],
        "metaPassPhone" => $demoData['passphone'],
    ];
    
    $emailSender = EmailSender::init(['email' => $userData['MBR_EMAIL'], 'name' => $userData['MBR_NAME']]);
    $emailSender->useFile("create-demo", $emailData);
    $send = $emailSender->send();
}

Logger::client_log([
    'mbrid' => $userData['MBR_ID'],
    'module' => "otp-verification",
    'message' => "OTP Verification",
    'device' => implode(", ", array_values(json_decode($_POST['device'] ?? ""))),
    'data' => array_merge($data, ($emailData ?? []))
]);

$db->commit();
ApiResponse([
    'status' => true,
    'message' => "Verifikasi OTP berhasil",
    'response' => []
]);