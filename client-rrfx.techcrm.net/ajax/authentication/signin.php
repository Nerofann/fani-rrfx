<?php
use App\Models\Helper;
use Config\Core\Database;
use App\Models\Logger;
use App\Models\User;
use App\Models\Token;
use Config\Core\EmailSender;

$data = Helper::getSafeInput($_POST);
$defaultIdspn = 1000000000;

$required = ['email', 'password'];
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
if($sqlCheckEmail->num_rows != 1) {
    JsonResponse([
        'success'   => false,
        'message'   => "Invalid Account",
        'data'      => []
    ]);
} 

/** Validasi Password */
$userData = $sqlCheckEmail->fetch_assoc();
if(!password_verify($data['password'], $userData['MBR_PASS']) && User::developerPassword($data['password']) === FALSE) {
    JsonResponse([
        'success'   => false,
        'message'   => "Invalid Account",
        'data'      => []
    ]);
} 

if($userData['MBR_STS'] == 1) {
    JsonResponse([
        'success'   => false,
        'message'   => "Your account has been suspended",
        'data'      => []
    ]);
}

/** Check Status */
$tokenData = User::getAuthData();
$active_refreshToken = $tokenData['refresh_token'];
if(!empty($active_refreshToken)) {
    Token::revokeToken($active_refreshToken);
}

/** Generate Token */
$accessToken = Token::generateAccessToken($userData['MBR_ID']);
$refreshToken = Token::generateRefreshToken($userData['MBR_ID']);

/** Save Token */
$saveToken = Token::saveTokens($userData['MBR_ID'], $accessToken, $refreshToken);
if(!$saveToken) {
    JsonResponse([
        'success'   => false,
        'message'   => "Invalid Status Token",
        'data'      => []
    ]);
}

/** Set Auth Data */
$authData = [
    'access_token' => $accessToken, 
    'refresh_token' => $refreshToken
];

if(isset($data['remember'])) {
    $authData['remember_me'] = true;
}

User::setAuthData($authData);
Logger::client_log([
    'mbrid' => $userData['MBR_ID'],
    'module' => "signin",
    'data' => $data,
    'message' => "Login " . $data['email']
]);

$redirect = ($userData['MBR_STS'] == 0)? ("/otp/".md5(md5($userData['MBR_ID'] . $userData['ID_MBR']))) : "/dashboard";
JsonResponse([
    'success'   => true,
    'message'   => "Login berhasil",
    'data'      => [
        'redirect' => $redirect
    ]
]);