<?php
    
    use App\Models\Helper;
    use App\Models\Admin;
    use App\Models\Logger;
    use App\Models\FileUpload;
    use Config\Core\Database;
    
    $listGrup = $adminPermissionCore->availableGroup();
    $adminRoles = Admin::adminRoles();
    if(!$adminPermissionCore->hasPermission($authorizedPermission, "/transaction/withdrawal_accounting")) {
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Authorization Failed",
            'data'      => []
        ]);
    }

    $data = Helper::getSafeInput($_POST);
    foreach(["note", "acc-dpx", "acc-act"] as $req) {
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

    /** Check accept or reject */
    if(!in_array(strtolower($data["acc-act"]), ["accept", "reject"])){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Invalid action",
            'data'      => []
        ]);
    }

    /** Check withdrawal id */
    $SQL_CHECK = mysqli_query($db, '
        SELECT 
            tb_dpwd.ID_DPWD 
        FROM tb_dpwd 
        WHERE MD5(MD5(tb_dpwd.ID_DPWD)) = "'.$data["acc-dpx"].'" 
        AND tb_dpwd.DPWD_STS = 0
        AND tb_dpwd.DPWD_STSVER = -1
        AND tb_dpwd.DPWD_TYPE = 2
    ');
    if((!$SQL_CHECK) || $SQL_CHECK->num_rows == 0){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Withdrawal id not found",
            'data'      => []
        ]);
    }
    $RSLT_CHECK = $SQL_CHECK->fetch_assoc();

    $UPDATE_DATA = [
        "DPWD_NOTE1"     => $data["note"],
        "DPWD_TIMESTAMP" => date("Y-m-d H:i:s")
    ];
    switch (strtolower($data["acc-act"])) {
        case 'accept':
            $UPDATE_DATA["DPWD_STS"]  = -1;
            break;
        
        case 'reject':
            $UPDATE_DATA["DPWD_STS"]  = 1;
            break;
        
        default:
            JsonResponse([
                'code'      => 200,
                'success'   => false,
                'message'   => "Invalid action",
                'data'      => []
            ]);
        break;
    }

    /** Update data */
    $update = Database::update('tb_dpwd', $UPDATE_DATA, ["ID_DPWD" => $RSLT_CHECK["ID_DPWD"]]);
    if(!$update){
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Failed to ".$data["acc-act"],
            'data'      => []
        ]);
    }
    
    Logger::admin_log([
        'admid' => $user['ADM_ID'],
        'module' => "transaction/withdrawal/authorization",
        'message' => strtoupper($data["acc-act"])." Withdrawal",
        'data'  => $data
    ]);

    JsonResponse([
        'code'      => 200,
        'success'   => true,
        'message'   => "Success ".$data["acc-act"],
        'data'      => []
    ]);