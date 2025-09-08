<?php
use App\Models\Helper;
$data = Helper::getSafeInput($_GET);
$dt->query("
    SELECT
        TICKET,
        SYMBOL,
        VOLUME,
        OPEN_TIME AS OPENED,
        CLOSE_TIME AS CLOSED,
        TIME_TO_SEC(TIMEDIFF(v_mt5_trades.CLOSE_TIME, v_mt5_trades.OPEN_TIME)) AS SECOND,
        PROFIT
    FROM view_mt5_trades v_mt5_trades
    WHERE MD5(MD5(LOGIN)) = '".$data['login']."'
    AND TIME_TO_SEC(TIMEDIFF(v_mt5_trades.CLOSE_TIME, v_mt5_trades.OPEN_TIME)) <= 30
    AND DATE(v_mt5_trades.OPEN_TIME) >= DATE(FROM_UNIXTIME('".$data['startdate']."')) 
    AND DATE(v_mt5_trades.OPEN_TIME) < DATE_ADD(DATE(FROM_UNIXTIME('".$data['enddate']."')), INTERVAL 2 DAY)
");

echo $dt->generate()->toJson();