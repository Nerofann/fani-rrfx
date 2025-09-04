<?php

use App\Models\Helper;
use App\Models\Wilayah;

$district = Helper::form_input($_GET['district'] ?? "");
$villages = Wilayah::villages($district);
$result = [];
foreach($villages as $vil) {
    $result[] = [
        'name' => $vil['KDP_KELURAHAN'],
        'postalCode' => $vil['KDP_POS'],
        'selected' => (isset($user['MBR_VILLAGES']) && $vil['KDP_KELURAHAN'] == $user['MBR_VILLAGES'])? true : false,
    ];
}

ApiResponse([
    'status' => true,
    'message' => "Successfull",
    'response' => $result
]);