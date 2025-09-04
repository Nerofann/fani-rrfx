<?php

use App\Models\Helper;

exit(json_encode([
    'status' => true,
    'message' => 'Berhasil mengambil data profil',
    'response' => [
        "name" => $userData['MBR_NAME'],
        "email" => $userData['MBR_EMAIL'],
        "phone" => $userData['MBR_PHONE'],
        "gender" => $userData['MBR_JENIS_KELAMIN'],
        "city" => $userData['MBR_CITY'],
        "country" => $userData['MBR_COUNTRY'],
        "address" => $userData['MBR_ADDRESS'],
        "zip" => $userData['MBR_ZIP'],
        "tgl_lahir" => Helper::default_date($userData['MBR_TGLLAHIR'], "Y-m-d"),
        "tmpt_lahir" => $userData['MBR_TMPTLAHIR'],
        "type_id" => $userData['MBR_TYPE_IDT'],
        "id_number" => $userData['MBR_NO_IDT'],
        "url_photo" => $avatar,
        "status" => $userData['MBR_STS'],
        "ver" => $userData['MBR_VERIF']
    ]
]));

