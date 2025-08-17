<?php

use App\Models\Helper;

$data = Helper::getSafeInput($_POST);

/** check terms & condition */
if(empty($data['terms'])) {
    JsonResponse([
        'success' => false,
        'message' => "Please agree the Terms & Policy",
        'data' => []
    ]);
}

if(validate_become_ib($user['MBR_ID']) !== true) {
    JsonResponse([
        'success' => false,
        'message' => "Has not met the requirements to submit IB",
        'data' => []
    ]);
}