<?php

use App\Models\Helper;

ApiResponse([
    'status' => true,
    'message' => 'Berhasil mengambil data profil',
    'response' => [
        "name" => $user['MBR_NAME'],
        "email" => $user['MBR_EMAIL'],
        "phone" => $user['MBR_PHONE'],
        "gender" => $user['MBR_JENIS_KELAMIN'],
        "city" => $user['MBR_CITY'],
        "country" => $user['MBR_COUNTRY'],
        "address" => $user['MBR_ADDRESS'],
        "zip" => $user['MBR_ZIP'],
        "tgl_lahir" => Helper::default_date($user['MBR_TGLLAHIR'], "Y-m-d"),
        "tmpt_lahir" => $user['MBR_TMPTLAHIR'],
        "type_id" => $user['MBR_TYPE_IDT'],
        "id_number" => $user['MBR_NO_IDT'],
        "url_photo" => $avatar,
        "status" => $user['MBR_STS'],
        "ver" => $user['MBR_VERIF']
    ]
]);