<?php

$dbmetasrv = 'meta_rrfxdemo';
$dt->query("
    SELECT
        v_mt5_users.LOGIN,
        v_mt5_users.BALANCE AS AMOUNT
    FROM tb_racc
    JOIN {$dbmetasrv}.mt5_users v_mt5_users ON(tb_racc.ACC_LOGIN = v_mt5_users.LOGIN)
    ORDER BY AMOUNT DESC
    LIMIT 10
");
$dt->edit("AMOUNT", function($col) {
    return number_format($col['AMOUNT'], 2);
});

echo $dt->generate()->toJson();