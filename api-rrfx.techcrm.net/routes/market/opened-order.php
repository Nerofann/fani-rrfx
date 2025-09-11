<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;

$apiTerminal = MetatraderFactory::apiTerminal();
$accountLogin = Helper::form_input($_GET['login'] ?? "");
$account = Account::realAccountDetail_byLogin($accountLogin);
if(!$account || $account['ACC_MBR'] != $user['MBR_ID']) {
    ApiResponse([
        'status' => false,
        'message' => 'Invalid Account',
        'response' => []
    ]);
}

/** Get Token */
$token = MetatraderFactory::autoConnect($account['ACC_LOGIN']);
if(!$token) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Token",
        'response' => []
    ]);
}

/** Get Trade History */
$tradeHistory = $apiTerminal->openedOrders(['id' => $token]);
if($tradeHistory->success === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => $tradeHistory->message,
        'response' => []
    ]);
}

$result = [];
foreach($tradeHistory->data as $trade) {
    $result[] = [
        'ticket' => $trade->ticket,
        'lot' => $trade->volume,
        'openPrice' => $trade->openPrice,
        'currentPrice' => $trade->price_current,
        'openTime' => $trade->openTime,
        'stopLoss' => $trade->stopLoss,
        'takeProfit' => $trade->takeProfit,
        'swap' => $trade->swap,
        'profit' => $trade->profit,
        'orderType' => $trade->orderType,
        'digits' => $trade->digits
    ];
}

ApiResponse([
    'status' => true,
    'message' => 'Opened Orders',
    'response' => $result
]);
