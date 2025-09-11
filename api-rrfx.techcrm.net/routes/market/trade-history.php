<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;

$apiTerminal = MetatraderFactory::apiTerminal();
$accountLogin = Helper::form_input($_GET['login'] ?? "");
$account = Account::realAccountDetail_byLogin($accountLogin);
if(empty($account) || $account['ACC_MBR'] != $user['MBR_ID']) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Account",
        'response' => []
    ], 400);
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
$tradeHistory = $apiTerminal->historyOrders(['id' => $token, 'from' => date("Y-m-d", strtotime("--7 day")), 'to' => date("Y-m-d")]);
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
        'profit' => $trade->profit,
        'closePrice' => $trade->closePrice,
        'closeTime' => $trade->closeTime,
        'openPrice' => $trade->openPrice,
        'openTime' => $trade->openTime,
        'lot' => $trade->lots,
        'orderType' => $trade->orderType,
        'symbol' => $trade->symbol,
        'stopLoss' => $trade->stopLoss,
        'takeProfit' => $trade->takeProfit,
        'digits' => $trade->digits,
    ];
}

ApiResponse([
    'status' => true,
    'message' => 'Trade History',
    'response' => $result
]);
