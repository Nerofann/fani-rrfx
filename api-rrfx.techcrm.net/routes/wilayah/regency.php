<?php

use App\Models\Helper;
use App\Models\Wilayah;

$province = Helper::form_input($_GET['province'] ?? "");
$regency = Wilayah::regency($province);
$result = [];
foreach($regency as $reg) {
    $result[] = [
        'name' => $reg,
        'selected' => (isset($user['MBR_CITY']) && strtoupper($reg) == strtoupper($user['MBR_CITY']))? true : false
    ];
}

ApiResponse([
    'status' => true,
    'message' => "Successfull",
    'response' => $result
]);