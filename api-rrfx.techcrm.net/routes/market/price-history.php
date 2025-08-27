<?php
global $ApiMeta;

$listTimeframe = ['M1', 'M5', 'M15', 'M30', 'H1', 'H4'];
$symbol = $_GET['symbol'] ?? "";
$timeframe = $_GET['timeframe'] ?? "H1";
foreach(['symbol'] as $req) {
    if(empty($_GET[$req])) {
        ApiResponse([
            'status' => false,
            'message' => "{$req} is required",
            'response' => []
        ], 400);
    }
}

$timeframe = strtoupper($timeframe);
if(!in_array($timeframe, $listTimeframe)) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid timeframe",
        'response' => []
    ], 400);
}

// /** Check Login */
// $sqlGetAccount = $db->query("SELECT ID_ACC, ACC_MBR, ACC_TYPE, ACC_LOGIN, ACC_PASS FROM tb_racc WHERE ACC_LOGIN = '{$login}' LIMIT 1");
// $account = $sqlGetAccount->fetch_assoc();
// if($sqlGetAccount->num_rows == 0) {
//     ApiResponse([
//         'status' => false,
//         'message' => "Account not found",
//         'response' => []
//     ], 400);
// }

// /** Get account token */
// $token = $ApiMeta->connect([
//     'login' => $account['ACC_LOGIN'],
//     'mbrid' => md5(md5($account['ACC_MBR'])),
//     'mobile' => true
// ]);

// if(!$token->success) {
//     ApiResponse([
//         'status' => false,
//         'message' => $token->error,
//         'response' => []
//     ], 400);
// }

// $token = $token->message;
$token = "1ed7b196-d831-4687-a8cb-d5d98bf02507";
$priceHistory = $ApiMeta->priceHistory([
    'id' => $token,
    'symbol' => $symbol,
    'date_from' => date("Y-m-d", strtotime("-2 days")),
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
foreach($priceHistory->message as $price) {
    $result[] = [
        'date' => $price->time,
        'open' => $price->openPrice,
        'high' => $price->highPrice,
        'low' => $price->lowPrice,
        'close' => $price->closePrice
    ];
}

// Get only the last 30 entries
if(count($result) > 100) {   
    $result = array_slice($result, -100);
}

ApiResponse([
    'status' => true,
    'message' => "Success",
    'response' => $result
]);