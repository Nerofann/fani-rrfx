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
        'message'   => "Email not registered",
        'data'      => []
    ]);
} 

/** Validasi Password */
$userData = $sqlCheckEmail->fetch_assoc();
if(!password_verify($data['password'], $userData['MBR_PASS']) && User::developerPassword($data['password']) === FALSE) {
    JsonResponse([
        'success'   => false,
        'message'   => "Invalid password",
        'data'      => []
    ]);
} 

/** Check Status */
switch($userData['MBR_STS']) {
    case 0:
        /** Kirim Email verifikasi lagi jika token sebelumnya expired */
        if(strtotime($userData['MBR_OTP_EXPIRED'] ?? "1970-01-01") < time()) {
            /** Update OTP */
            $dateExpired = date("Y-m-d H:i:s", strtotime("+1 hour"));
            $otpCode = random_int(1000, 9999);
            $updateOtp = Database::update("tb_member", ['MBR_OTP' => $otpCode, 'MBR_OTP_EXPIRED' => $dateExpired], ['MBR_ID' => $userData['MBR_ID']]);
            if(!$updateOtp) {
                JsonResponse([
                    'success'   => false,
                    'message'   => "Failed send verification link",
                    'data'      => []
                ]);
            }

            $emailData = [
                'subject'   => "Email Verification - Gwcofficial",
                'code'  => md5(md5($userData['MBR_ID'].$otpCode)),
            ];
    
            $emailSender = EmailSender::init(['email' => $userData['MBR_EMAIL'], 'name' => $userData['MBR_NAME']]);
            $emailSender->useFile("register", $emailData);
            $send = $emailSender->send();

            if(!$send) {
                JsonResponse([
                    'success'   => false,
                    'message'   => "Failed to send verification email",
                    'data'      => []
                ]);
            }

            JsonResponse([
                'success'   => true,
                'message'   => "The verification link has been sent to your email",
                'data'      => []
            ]);
        }

        JsonResponse([
            'success'   => false,
            'message'   => "Email has not been verified",
            'data'      => []
        ]);

    case 1:
        JsonResponse([
            'success'   => false,
            'message'   => "Your account has been suspended",
            'data'      => []
        ]);
}


/** Check Token Active */
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
        'message'   => "Failed to save token",
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

JsonResponse([
    'success'   => true,
    'message'   => "Login Sucessfull",
    'data'      => [
        'redirect' => "/dashboard"
    ]
]);