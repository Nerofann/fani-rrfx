<?php

use App\Models\Helper;
use App\Models\Token;
use App\Models\User;

$status = [
    '0' => "otp",
    '1' => "suspend",
    '2' => "verification",
    '-1' => "active"
];

$refreshToken = Helper::form_input($_POST['refresh_token']);
if(empty($refreshToken)) {
    ApiResponse([
        'status' => false,
        'message' => 'Refresh token is required',
        'response' => []
    ], 400);
}

// Verify refresh token
$payload = Token::verifyToken($refreshToken);
if (!$payload) {
    ApiResponse([
        'status' => false,
        'message' => 'Invalid refresh token',
        'response' => []
    ], 400);
}

// Find token in database
$tokenRecord = Token::findValidRefreshToken($refreshToken);
if (!$tokenRecord) {
    ApiResponse([
        'status' => false,
        'message' => 'Refresh token expired or revoked',
        'response' => []
    ], 400);
}

/** Revoke old token  */
$revokeToken = Token::revokeToken($refreshToken);
if(!$revokeToken) {
    ApiResponse([
        'status' => false,
        'message' => 'Invalid Refresh',
        'response' => []
    ], 400);
}

/** Generate new token */
$accessToken = Token::generateAccessToken($payload['user_id']);
$refreshToken = Token::generateRefreshToken($payload['user_id']);

/** Save new token */
$saveToken = Token::saveTokens($payload['user_id'], $accessToken, $refreshToken);
if(!$saveToken) {
    ApiResponse([
        'status' => false,
        'message' => 'Invalid Save Token',
        'response' => []
    ], 400);
}

/** find user */
$user = User::findByMemberId($payload['user_id']);
if(!$user) {
    ApiResponse([
        'status' => false,
        'message' => 'User not found',
        'response' => []
    ], 400);
}

ApiResponse([
    'status' => true,
    'message' => 'Berhasil menemukan akun',
    'response' => array(
        "access_token" => $accessToken,
        "refresh_token" => $refreshToken,
        "expires_in" => ACCESS_TOKEN_LIFETIME,
        "status" => $status[ $user['MBR_STS'] ]
    )
], 200);