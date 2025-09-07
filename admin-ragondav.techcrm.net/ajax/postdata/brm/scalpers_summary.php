<?php
use App\Models\Helper;
$data   = Helper::getSafeInput($_GET);

$enddate   = isset($data['enddate']) ? (int)$data['enddate'] : time();
$startdate = isset($data['startdate']) ? (int)$data['startdate'] : strtotime('-7 days', $enddate);

$trades = 0;
$profit = 0;
$sqlGet = $db->query('
    SELECT
        IFNULL(COUNT(v_mt5_trades.LOGIN), 0) AS COUNT_TRADES,
        IFNULL(SUM(v_mt5_trades.PROFIT), 0) AS SUM_PROFIT
    FROM view_mt5_trades v_mt5_trades
    JOIN view_mt5_users v_mt5_users ON(v_mt5_users.login = v_mt5_trades.LOGIN)
    WHERE TIME_TO_SEC(TIMEDIFF(v_mt5_trades.CLOSE_TIME, v_mt5_trades.OPEN_TIME)) <= 30
    AND DATE(v_mt5_trades.OPEN_TIME) >= DATE(FROM_UNIXTIME('.$startdate.')) 
    AND DATE(v_mt5_trades.OPEN_TIME) < DATE_ADD(DATE(FROM_UNIXTIME('.$enddate.')), INTERVAL 2 DAY)
');

if($sqlGet) {
    $data = $sqlGet->fetch_assoc();
    $trades = (int)$data['COUNT_TRADES'];
    $profit = (float)$data['SUM_PROFIT'];
}

JsonResponse([
    'success' => true,
    'message' => "Successfull",
    'data' => [
        'trades' => number_format($trades, 0),
        'profit' => number_format($profit, 2)
    ]
]);