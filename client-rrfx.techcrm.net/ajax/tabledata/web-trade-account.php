<?php
$mbrid = $user['MBR_ID'];
$dt->query("
    SELECT 
        tr.ACC_LOGIN,
        trt.RTYPE_LEVERAGE,
        mt5u.MARGIN_FREE
    FROM tb_racc tr
    JOIN tb_racctype trt ON (trt.ID_RTYPE = tr.ACC_TYPE)
    JOIN mt5_users mt5u ON (mt5u.LOGIN = tr.ACC_LOGIN)
    WHERE tr.ACC_MBR = {$mbrid}
");

$dt->edit('MARGIN_FREE', fn($col) :string => App\Models\Helper::formatCurrency($col['MARGIN_FREE']) . " USD");
$dt->add('ACTION', fn($col) :string => '<a class="btn btn-sm btn-success text-white"><i class="fas fa-lock"></i></a>');

echo $dt->generate()->toJson();