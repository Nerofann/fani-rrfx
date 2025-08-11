<?php
    
    use App\Models\Account;
    use App\Models\Dpwd;
    use App\Models\Helper;
    use App\Models\Admin;
    use App\Models\Logger;
    use App\Models\FileUpload;
    use Config\Core\Database;
    
    $listGrup = $adminPermissionCore->availableGroup();
    $adminRoles = Admin::adminRoles();
    if(!$adminPermissionCore->hasPermission($authorizedPermission, "/account/edit_pekerjaan")) {
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Authorization Failed",
            'data'      => []
        ]);
    }

    $REQ_POST = [
        "sbmt_id",
        "k-type-pekerjaan",
        "k-nama-perusahaan",
        "k-bidang-pekerjaan",
        "k-jabatan",
        "k-nomor-telepon-kantor",
        "k-lama-bekerja",
        "k-kantor-sebelumnya",
        "k-alamat-tempat-kerja",
        "k-kode-pos-kantor",
        "k-fax-kantor"
    ];
    $data = Helper::getSafeInput($_POST);
    foreach($REQ_POST as $req) {
        if(in_array($req, ["k-fax-kantor"])){
            if(!isset($data[ $req ])) {
                $req = str_replace("add_", "", $req);
                JsonResponse([
                    'code'      => 402,
                    'success'   => false,
                    'message'   => "{$req} diperlukan",
                    'data'      => []
                ]);
            }
        }else{
            if(empty($data[ $req ])) {
                $req = str_replace("add_", "", $req);
                JsonResponse([
                    'code'      => 402,
                    'success'   => false,
                    'message'   => "{$req} diperlukan",
                    'data'      => []
                ]);
            }
        }
    }

    /** Check Id Account */
    $ACCOUNT_CHECK = Account::realAccountDetail($data["sbmt_id"]);
    if(!$ACCOUNT_CHECK){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Account id not found",
            'data'      => []
        ]);
    }

    /** Check Valid Account to proceeds */
    if(($ACCOUNT_CHECK["ACC_STS"] != -1 || $ACCOUNT_CHECK["ACC_WPCHECK"] != 6)){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Invalid Account",
            'data'      => []
        ]);
    }

    /**Stored data for update*/
    $UPDATE_DATA = [
        "ACC_F_APP_KRJ_TYPE"      => $data["k-type-pekerjaan"],
        "ACC_F_APP_KRJ_NAMA"      => $data["k-nama-perusahaan"],
        "ACC_F_APP_KRJ_BDNG"      => $data["k-bidang-pekerjaan"],
        "ACC_F_APP_KRJ_JBTN"      => $data["k-jabatan"],
        "ACC_F_APP_KRJ_TLP"       => $data["k-nomor-telepon-kantor"],
        "ACC_F_APP_KRJ_LAMA"      => $data["k-lama-bekerja"],
        "ACC_F_APP_KRJ_LAMASBLM"  => $data["k-kantor-sebelumnya"],
        "ACC_F_APP_KRJ_ALAMAT"    => $data["k-alamat-tempat-kerja"],
        "ACC_F_APP_KRJ_ZIP"       => $data["k-kode-pos-kantor"],
        "ACC_F_APP_KRJ_FAX"       => $data["k-fax-kantor"]
    ];

    /**Eksekusi database*/
    $update = Database::update('tb_racc', $UPDATE_DATA, ["ID_ACC" => $ACCOUNT_CHECK["ID_ACC"]]);
    if(!$update){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Failed to update data.",
            'data'      => []
        ]);
    }

    Logger::admin_log([
        'admid' => $user['ADM_ID'],
        'module' => "account/active_real_account/edit",
        'message' => "Edit pekerjaan",
        'data'  => $data
    ]);

    JsonResponse([
        'code'      => 200,
        'success'   => true,
        'message'   => "Success Update Data",
        'data'      => []
    ]);