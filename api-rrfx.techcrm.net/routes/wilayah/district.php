<?php

use App\Models\Helper;
use App\Models\Wilayah;

$regency = Helper::form_input($_GET['regency'] ?? "");
$district = Wilayah::district($regency);
$result = [];
foreach($district as $dis) {
    $result[] = [
        'name' => $dis,
        'selected' => (isset($user['MBR_DISTRICT']) && strtoupper($dis) == strtoupper($user['MBR_DISTRICT']))? true : false
    ];
}

ApiResponse([
    'status' => true,
    'message' => "Successfull",
    'response' => $result
]);