<?php

$login = form_input($_GET['login']);
if(empty($login)) {
    ApiResponse([
        'status' => false,
        'message' => 'Login is required',
        'response' => []
    ]);
}

/** Get Token */
$token = $ApiMeta->connect(['login' => $login, 'mbrid' => md5(md5($userData['MBR_ID'])), 'mobile' => true]);
if($token->success === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => $token->error,
        'response' => []
    ]);
}

/** Get Trade History */
$tradeHistory = $ApiMeta->openedOrders(['id' => $token->message]);
if($tradeHistory->success === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => $tradeHistory->error,
        'response' => []
    ]);
}

ApiResponse([
    'status' => true,
    'message' => 'Opened Orders',
    'response' => $tradeHistory->message
]);
