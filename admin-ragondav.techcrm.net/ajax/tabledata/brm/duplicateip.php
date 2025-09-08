<?php
    $dt->query('
        SELECT
            COUNT(ip) AS TOTALIP,
            ip AS IP,
            MD5(MD5(ip)) AS HIDIP
        FROM view_mt5_users
        WHERE ip <> ""
        GROUP BY ip
        HAVING TOTALIP > 1
    ');
    
    // $dt->edit("HIDIP", function($col) {
    //     return '<a href="/brm/duplicateip/detail/'.md5(md5($col['HIDIP'])).'" class="btn btn-sm btn-primary">Detail</a>';
    // });


    echo $dt->generate()->toJson();