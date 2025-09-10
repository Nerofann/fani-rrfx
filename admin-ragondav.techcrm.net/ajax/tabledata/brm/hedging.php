<?php
use App\Models\Helper;
$data = Helper::getSafeInput($_GET);

$enddate   = isset($data['enddate']) ? (int)$data['enddate'] : time();
$startdate = isset($data['startdate']) ? (int)$data['startdate'] : strtotime('-7 days', $enddate);
    $dt->query('
        SELECT
            n0.login AS LOGIN,
            CONCAT(n0.position_id, "_", n1.position_id) AS GROUP_HEDGING,
            n0.symbol AS SYMBOL
        FROM (
            SELECT
                position_id, login, action, symbol, volume, time_create,
                ROW_NUMBER() OVER (
                    PARTITION BY login, action
                    ORDER BY time_create, position_id
                ) AS rn
            FROM `view_mt5_positions` AS v_mt5_positions
            WHERE action IN (0,1)
            AND DATE(FROM_UNIXTIME(time_create)) >= DATE(FROM_UNIXTIME('.$startdate.')) 
            AND DATE(FROM_UNIXTIME(time_create)) < DATE_ADD(DATE(FROM_UNIXTIME('.$enddate.')), INTERVAL 2 DAY)
        ) AS n0
        JOIN (
            SELECT
                position_id, login, action, symbol, volume, time_create,
                ROW_NUMBER() OVER (
                    PARTITION BY login, action
                    ORDER BY time_create, position_id
                ) AS rn
            FROM `view_mt5_positions` AS v_mt5_positions
            WHERE action IN (0,1)
            AND DATE(FROM_UNIXTIME(time_create)) >= DATE(FROM_UNIXTIME('.$startdate.')) 
            AND DATE(FROM_UNIXTIME(time_create)) < DATE_ADD(DATE(FROM_UNIXTIME('.$enddate.')), INTERVAL 2 DAY)
        ) AS n1
        ON n0.login  = n1.login
        AND n0.action = 0
        AND n1.action = 1
        AND n0.rn     = n1.rn
        AND n0.symbol = n1.symbol
        ORDER BY n0.login, n0.rn
    ');
    
    // $dt->edit("HIDIP", function($col) {
    //     return '<a href="/brm/duplicateip/detail/'.md5(md5($col['HIDIP'])).'" class="btn btn-sm btn-primary">Detail</a>';
    // });


    echo $dt->generate()->toJson();