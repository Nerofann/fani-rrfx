<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;

$apiTerminal = MetatraderFactory::apiTerminal();
$ticket = Helper::form_input($_POST['ticket'] ?? "");
$login = Helper::form_input($_POST['account'] ?? "");
if(empty($ticket)) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Ticket",
        'data' => []
    ]);
}

/** check Account */
$account = Account::realAccountDetail_byLogin($login);
if(!$account || $account['ACC_MBR'] != $user['MBR_ID']) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Account",
        'data' => []
    ]);
}

/** Connect */
$token = $apiTerminal->connect(['login' => $account['ACC_LOGIN'], 'password' => $account['ACC_PASS']]);
if(!$token) {
    JsonResponse([
        'success' => false,
        'message' => "Connection Failed",
        'data' => []
    ]);
}

/** Close Order */
$closeData = [
    'id' => $token,
    'ticket' => $ticket
];

$orderClose = $apiTerminal->orderClose($closeData);
if(!$orderClose->success) {
    JsonResponse([
        'success' => false,
        'message' => $orderClose->message ?? "Gagal menutup order",
        'data' => []
    ]);
}

JsonResponse([
    'success' => true,
    'message' => "Berhasil",
    'data' => $orderClose->data
]);