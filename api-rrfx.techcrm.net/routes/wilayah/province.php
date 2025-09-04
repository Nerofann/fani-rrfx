<?php

use App\Models\Helper;
use App\Models\Wilayah;

$province = Helper::form_input($_GET['province'] ?? "");
$provinces = Wilayah::provinces();
$result = [];
foreach($provinces as $prv) {
    $result[] = [
        'name' => $prv,
        'selected' => (isset($user['MBR_PROVINCE']) && strtoupper($prv) == strtoupper($user['MBR_PROVINCE']))? true : false
    ];
}

ApiResponse([
    'status' => true,
    'message' => "Successfull",
    'response' => $result
]);