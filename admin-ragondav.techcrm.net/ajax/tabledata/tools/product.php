<?php
$dt->query("
    SELECT
        RTYPE_SUFFIX,
        RTYPE_NAME,
        RTYPE_KOMISI,
        RTYPE_ISFLOATING,
        RTYPE_RATE,
        RTYPE_CURR,
        RTYPE_TYPE,
        RTYPE_GROUP,
        RTYPE_FILE,
        RTYPE_STS
    FROM tb_racctype
    WHERE RTYPE_SUFFIX != '000'
");

$dt->hide("RTYPE_KOMISI");
$dt->hide("RTYPE_ISFLOATING");
$dt->hide("RTYPE_RATE");
$dt->hide("RTYPE_CURR");
$dt->edit("RTYPE_TYPE", function($col) {
    $rate = $col['RTYPE_ISFLOATING']? "Floating" : App\Models\Helper::formatCurrency($col['RTYPE_RATE']);

    return '
        <p class="mb-0">Rate: <b>'.$rate.'</b></p>
        <p class="mb-0">Currency: <b>'.$col['RTYPE_CURR'].'</b></p>
        <p class="mb-0">Komisi: <b>'.$col['RTYPE_KOMISI'].'</b></p>
    ';
});

$dt->edit("RTYPE_FILE", fn($col): string => '<a href="https://client-rrfx.techcrm.net/assets/trading-rules/'.$col['RTYPE_FILE'].'" class="text-decoration-underline">Lihat</a>');
$dt->edit("RTYPE_STS", fn($col): string => App\Models\Product::$status[ $col['RTYPE_STS'] ]['html']);
$dt->add("ACTION", function($col) {
    return '<div class="action justify-content-center d-flex gap-2" data-suffix="'.$col['RTYPE_SUFFIX'].'"></div>';
});

echo $dt->generate()->toJson();