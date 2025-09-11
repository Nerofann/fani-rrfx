<?php
    
    use App\Models\Helper;
    use App\Models\Admin;
    use App\Models\Logger;
    use Config\Core\Database;

    $listGrup = $adminPermissionCore->availableGroup();
    $adminRoles = Admin::adminRoles();
    if(!$adminPermissionCore->hasPermission($authorizedPermission, "/member/delete_user_action")) {
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Authorization Failed",
            'data'      => []
        ]);
    }

    $data = Helper::getSafeInput($_POST);
    foreach(['xid', 'val'] as $req) {
        if(empty($data[ $req ])) {
            // $req = str_replace("add-", "", $req);
            JsonResponse([
                'success'   => false,
                'message'   => "{$req} diperlukan",
                'data'      => []
            ]);
        }
    }

    /** Check accept or reject */
    if(!in_array(strtolower($data["val"]), ["accept", "reject"])){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Invalid action",
            'data'      => []
        ]);
    }

    /** Check ID */ 
    $SQL_CHECK = $db->query('SELECT tb_dlt_account.ID_DLTACC FROM tb_dlt_account WHERE MD5(MD5(tb_dlt_account.ID_DLTACC)) = "'.$data['xid'].'"');
    if($SQL_CHECK->num_rows == 0){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "ID not found",
            'data'      => []
        ]);
    }
    $RSLT_CHECK = $SQL_CHECK->fetch_assoc();

    $UPDATE_DATA = [
        "DLTACC_STS"    => ($data["val"] == 'accept') ? -1 : (($data["val"] == 'reject') ? 1 : 0)
    ];

    $update = Database::update('tb_dlt_account', $UPDATE_DATA, ["ID_DLTACC" => $RSLT_CHECK["ID_DLTACC"], "DLTACC_STS" => 0]);
    if(!$update){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Failed to update data!.",
            'data'      => []
        ]);
    }

    Logger::admin_log([
        'admid' => $user['ADM_ID'],
        'module' => "delete_user",
        'message' => ucfirst($data["val"])." delete user request",
        'data'  => $data
    ]);

    JsonResponse([
        'success'   => true,
        'message'   => "Success ".$data["val"]." delete user request",
        'data'      => []
    ]);

