<?php
use App\Models\Helper;
$data = Helper::getSafeInput($_GET);
$dt->query("
    SELECT
        login AS LOGIN,
        FROM_UNIXTIME(lastaccess) AS LASTACCESS
    FROM view_mt5_users
    WHERE MD5(MD5(view_mt5_users.ip)) = '".$data['ip']."'
");

echo $dt->generate()->toJson();