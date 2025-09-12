<?php
$dbmetasrv = 'meta_rrfxdemo';
$dt->query("
    SELECT
        v_mt5_deals.login AS LOGIN,
        IFNULL(SUM(v_mt5_deals.volume/10000), 0) AS AMOUNT
    FROM tb_racc
    JOIN {$dbmetasrv}.mt5_deals v_mt5_deals ON(v_mt5_deals.login = tb_racc.ACC_LOGIN)
    WHERE v_mt5_deals.entry = 1
    GROUP BY v_mt5_deals.login
    ORDER BY AMOUNT DESC
    LIMIT 10
");
$dt->edit("AMOUNT", function($col) {
    return number_format($col['AMOUNT'], 2);
});

echo $dt->generate()->toJson();