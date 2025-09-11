<?php
use Allmedia\Shared\Metatrader\ApiVariable;
use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;
$apiTerminal = MetatraderFactory::apiTerminal();

$accountLogin = Helper::form_input($_GET['account'] ?? 0);
$symbol = Helper::form_input($_GET['symbol'] ?? "");
$timeframe = Helper::form_input($_GET['timeframe'] ?? "H1");
foreach(['symbol', 'account'] as $req) {
    if(empty($_GET[$req])) {
        ApiResponse([
            'status' => false,
            'message' => "{$req} is required",
            'response' => []
        ], 400);
    }
}

$timeframe = strtoupper($timeframe);
if(!in_array($timeframe, ApiVariable::$timeframes)) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid timeframe",
        'response' => []
    ], 400);
}

/** Check Account */
$account = Account::realAccountDetail_byLogin($accountLogin);
if(empty($account) || $account['ACC_MBR'] != $user['MBR_ID']) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Account",
        'response' => []
    ], 400);
}

$token = MetatraderFactory::autoConnect($account['ACC_LOGIN']);
if(!$token) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Connection",
        'response' => []
    ], 404);
}

$priceHistory = $apiTerminal->priceHistory([
    'id' => $token,
    'symbol' => $symbol,
    'date_from' => date("Y-m-d", strtotime("-3 days")),
    'date_to' => date("Y-m-d", strtotime("+1 day")),
    'timeframe' => $timeframe
]);

if(!$priceHistory->success) {
    ApiResponse([
        'status' => false,
        'message' => $priceHistory->error,
        'response' => []
    ], 400);
}

$result = [];
foreach($priceHistory->data as $price) {
    $result[] = [
        'time' => $price->time,
        'open' => $price->openPrice,
        'high' => $price->highPrice,
        'low' => $price->lowPrice,
        'close' => $price->closePrice,
        'digits' => $price->digits,
        'tickVolume' => $price->tickVolume
    ];
}

// Get only the last 30 entries
if(count($result) > 100) {   
    $result = array_slice($result, -1000);
}

ApiResponse([
    'status' => true,
    'message' => "Success",
    'response' => $result
]);