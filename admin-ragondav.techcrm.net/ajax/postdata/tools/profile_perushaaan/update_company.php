<?php
    
    use App\Models\Helper;
    use App\Models\Admin;
    use App\Models\Logger;
    use App\Models\FileUpload;
    use Config\Core\Database;
    
    $listGrup = $adminPermissionCore->availableGroup();
    $adminRoles = Admin::adminRoles();
    if(!$adminPermissionCore->hasPermission($authorizedPermission, "/tools/profile_perushaaan/update_company")) {
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Authorization Failed",
            'data'      => []
        ]);
    }

    $REQ_POST = [
        "president-direktur",
        "direktur-kepatuhan",
        "direktur-operational",
        "komisaris-utama",
        "komisaris",
        "sp-merge",
        "no_izin_usaha",
        "tgl_izin_usaha",
        "no_keanggotaan_bursa",
        "tgl_keanggotaan_bursa",
        "no_keanggotaan_lembaga",
        "tgl_keanggotaan_lembaga",
        "no_persetujuan_peserta",
        "tgl_persetujuan_peserta",
        "faxmail"
    ];
    $data = Helper::getSafeInput($_POST);
    foreach($REQ_POST as $req) {
        if(empty($data[ $req ])) {
            $req = str_replace("edit_", "", $req);
            JsonResponse([
                'code'      => 402,
                'success'   => false,
                'message'   => "{$req} diperlukan",
                'data'      => []
            ]);
        }
    }

    /** Check Company id*/
    $SQL_CHECK = mysqli_query($db, 'SELECT tb_profile.ID_PROF FROM tb_profile');
    if((!$SQL_CHECK) || $SQL_CHECK->num_rows == 0){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Cannot found current dtc id",
            'data'      => []
        ]);
    }
    $RSLT_GETX = $SQL_CHECK->fetch_assoc();

    
    $STORED_DATA = [
        "PROF_DEWAN_DIREKSI"             => $data["president-direktur"],
        "PROF_DIREKTUR"                  => $data["direktur-kepatuhan"],
        "PROF_OPERATIONAL"               => $data["direktur-operational"],
        "PROF_KOMISARIS_UTAMA"           => $data["komisaris-utama"],
        "PROF_KOMISARIS"                 => $data["komisaris"],
        "PROF_PEMEGANG_SAHAM"            => $data["sp-merge"],
        "PROF_NO_IZIN_USAHA"             => $data["no_izin_usaha"],
        "PROF_TGL_IZIN_USAHA"            => $data["tgl_izin_usaha"],
        "PROF_NO_KEANGGOTAAN_BURSA"      => $data["no_keanggotaan_bursa"],
        "PROF_TGL_KEANGGOTAAN_BURSA"     => $data["tgl_keanggotaan_bursa"],
        "PROF_NO_KEANGGOTAAN_LEMBAGA"    => $data["no_keanggotaan_lembaga"],
        "PROF_TGL_KEANGGOTAAN_LEMBAGA"   => $data["tgl_keanggotaan_lembaga"],
        "PROF_NO_PERSETUJUAN_PESERTA"    => $data["no_persetujuan_peserta"],
        "PROF_TGL_PERSETUJUAN_PESERTA"   => $data["tgl_persetujuan_peserta"],
        "PROF_FAX"                       => $data["faxmail"]
    ];

    
    /** Update data */
    $update = Database::update('tb_profile', $STORED_DATA, ["ID_PROF" => $RSLT_GETX["ID_PROF"]]);
    if(!$update){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Failed to update profile perushaan",
            'data'      => []
        ]);
    }

    Logger::admin_log([
        'admid' => $user['ADM_ID'],
        'module' => "/tools/profile_perushaaan/",
        'message' => "Update profile perusahaan",
        'data'  => $data
    ]);

    JsonResponse([
        'success'   => true,
        'message'   => "Berhasil update profile perushaan",
        'data'      => []
    ]);