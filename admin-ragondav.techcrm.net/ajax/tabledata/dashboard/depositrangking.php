<?php

$dbmetasrv = 'meta_rrfxdemo';
$dt->query("
    SELECT
        v_mt5_deals.login AS LOGIN,
        v_mt5_deals.profit AS AMOUNT
    FROM tb_racc
    JOIN {$dbmetasrv}.mt5_deals v_mt5_deals ON(tb_racc.ACC_LOGIN = v_mt5_deals.LOGIN)
    WHERE v_mt5_deals.action = 2
    ORDER BY AMOUNT DESC
    LIMIT 10
");
$dt->edit("AMOUNT", function($col) {
    return number_format($col['AMOUNT'], 2);
});

echo $dt->generate()->toJson();