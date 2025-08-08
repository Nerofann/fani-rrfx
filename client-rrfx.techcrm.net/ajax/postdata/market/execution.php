<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;

$apiTerminal = MetatraderFactory::apiTerminal();
$data = Helper::getSafeInput($_POST);
$required = [
    'account' => "Account",
    'symbol' => "Symbol",
    'type' => "Tipe",
    'volume' => "Volume"
];

foreach($required as $req => $text) {
    if(empty($data[ $req ])) {
        JsonResponse([
            'success' => false,
            'message' => "Kolom {$text} diperlukan",
            'data' => []
        ]);
    }
}

/** validasi account */
$account = Account::realAccountDetail_byLogin($data['account']);
if(!$account || $account['ACC_MBR'] != $user['MBR_ID']) {
    JsonResponse([
        'success' => false,
        'message' => "Account Not Found",
        'data' => []
    ]);
}

/** validasi tipe */
if(!in_array($data['type'], ["buy", "sell"])) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Type",
        'data' => []
    ]);
}

/** validasi volume */
$volume = Helper::stringTonumber($data['volume']);
if($volume <= 0) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Volume",
        'data' => []
    ]);
}

/** Metatrader Token */
$token = $apiTerminal->connect(['login' => $account['ACC_LOGIN'], 'password' => $account['ACC_PASS']]);
if(!$token) {
    JsonResponse([
        'success' => false,
        'message' => "Connection Failed",
        'data' => []
    ]);
}

$orderData = [
    'id' => $token,
    'symbol' => $data['symbol'],
    'operation' => $data['type'],
    'volume' => $volume,
];

$orderSend = $apiTerminal->orderSend($orderData);
if(!$orderSend->success) {
    JsonResponse([
        'success' => false,
        'message' => $orderSend->message ?? "Invalid Operation",
        'data' => []
    ]);
}

if(!$orderSend->success) {
    JsonResponse([
        'success' => false,
        'message' => $orderSend->message,
        'data' => []
    ]);
}

JsonResponse([
    'success' => true,
    'message' => "Berhasil #".$orderSend->data->ticket,
    'data' => $orderSend->data
]);