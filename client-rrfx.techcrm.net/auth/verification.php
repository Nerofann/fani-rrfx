<?php 
use App\Models\Helper;
use Config\Core\Database;
use App\Models\Token;
use App\Models\TokenGenerator;
use App\Models\User;

$data = Helper::getSafeInput($_GET);
$verificationCode = $data['b'] ?? "";

if(empty($verificationCode)) {
    die("<script>alert('Invalid code'); location.href = '/';</script>");
}

$sqlGet = $db->query("SELECT MBR_ID, MBR_OTP_EXPIRED FROM tb_member WHERE MD5(MD5(CONCAT(MBR_ID, MBR_OTP))) = '$verificationCode' LIMIT 1");
$userData = $sqlGet->fetch_assoc();
if($sqlGet->num_rows != 1) {
    die("<script>alert('Invalid verification code'); location.href = '/';</script>");
}

/** Check Code expired */
if(strtotime($userData['MBR_OTP_EXPIRED']) < strtotime("now")) {
    die("<script>alert('Verification code expired'); location.href = '/';</script>");
}

/** Update Sts */
$update = Database::update("tb_member", ['MBR_STS' => 2], ['MBR_ID' => $userData['MBR_ID']]);
if(!$update) {
    die("<script>alert('Verification failed'); location.href = '/';</script>");
}

/** Generate Token */
$accessToken = Token::generateAccessToken($userData['MBR_ID']);
$refreshToken = Token::generateRefreshToken($userData['MBR_ID']);

/** Save Token */
$saveToken = Token::saveTokens($userData['MBR_ID'], $accessToken, $refreshToken);
if(!$saveToken) {
    JsonResponse([
        'success'   => false,
        'message'   => "Failed to save token",
        'data'      => []
    ]);
}

/** Set Token to Cookie */
User::setAuthData([
    'access_token' => $accessToken,
    'refresh_token' => $refreshToken
]);

die("<script>alert('Verification Successfull'); location.href = '/dashboard';</script>");