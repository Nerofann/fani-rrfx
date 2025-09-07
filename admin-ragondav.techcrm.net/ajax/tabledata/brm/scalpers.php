<?php
use App\Models\Helper;
$data = Helper::getSafeInput($_GET);

$enddate   = isset($data['enddate']) ? (int)$data['enddate'] : time();
$startdate = isset($data['startdate']) ? (int)$data['startdate'] : strtotime('-7 days', $enddate);
    $dt->query('
        SELECT
            v_mt5_users.login LOGIN,
            v_mt5_users.`group` AS `GROUP`,
            SUM(IF(TIME_TO_SEC(TIMEDIFF(v_mt5_trades.CLOSE_TIME, v_mt5_trades.OPEN_TIME)) <= 30, 1, 0)) AS TOTAL_TRADES,
            SUM(IF(TIME_TO_SEC(TIMEDIFF(v_mt5_trades.CLOSE_TIME, v_mt5_trades.OPEN_TIME)) <= 30, 1, 0))/COUNT(v_mt5_trades.LOGIN) * 100 AS PERCENT_TRADES,
            SUM(IF(TIME_TO_SEC(TIMEDIFF(v_mt5_trades.CLOSE_TIME, v_mt5_trades.OPEN_TIME)) <= 30, v_mt5_trades.PROFIT, 0)) AS PROFIT_TRADES,
            MD5(MD5(v_mt5_users.login)) AS `DATA`
        FROM view_mt5_trades v_mt5_trades
        JOIN view_mt5_users v_mt5_users ON(v_mt5_users.login = v_mt5_trades.LOGIN)
        WHERE DATE(v_mt5_trades.OPEN_TIME) >= DATE(FROM_UNIXTIME('.$startdate.')) 
        AND DATE(v_mt5_trades.OPEN_TIME) < DATE_ADD(DATE(FROM_UNIXTIME('.$enddate.')), INTERVAL 2 DAY)
        GROUP BY login
        HAVING TOTAL_TRADES > 0
    ');
    
    // $dt->edit("HIDIP", function($col) {
    //     return '<a href="/brm/duplicateip/detail/'.md5(md5($col['HIDIP'])).'" class="btn btn-sm btn-primary">Detail</a>';
    // });


    echo $dt->generate()->toJson();