<?php
    
    use App\Models\Helper;
    use App\Models\Admin;
    use App\Models\Logger;
    use App\Models\User;
    use Config\Core\Database;
    use Config\Core\EmailSender;

    $listGrup = $adminPermissionCore->availableGroup();
    $adminRoles = Admin::adminRoles();
    if(!$adminPermissionCore->hasPermission($authorizedPermission, "/transaction/withdrawal_authorization")) {
        JsonResponse([
            'code'      => 200,
            'success'   => false,
            'message'   => "Authorization Failed",
            'data'      => []
        ]);
    }

    $data = Helper::getSafeInput($_POST);
    foreach(["note", "auth-dpx", "auth-act"] as $req) {
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
    if(!in_array(strtolower($data["auth-act"]), ["accept", "reject"])){
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
            tb_dpwd.ID_DPWD,
            DPWD_MBR,
            DPWD_CURR_FROM,
            DPWD_AMOUNT_SOURCE
        FROM tb_dpwd 
        WHERE MD5(MD5(tb_dpwd.ID_DPWD)) = "'.$data["auth-dpx"].'" 
        AND tb_dpwd.DPWD_STS = 0
        AND tb_dpwd.DPWD_STSVER = 0
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

    /** check user */
    $userdata = User::findByMemberId($RSLT_CHECK['DPWD_MBR']);
    if(!$userdata) {
        JsonResponse([
            'success' => false,
            'message' => "Invalid User",
            'data' => []
        ]);
    }

    $UPDATE_DATA = [
        "DPWD_NOTE1"     => $data["note"],
        "DPWD_DATETIME2" => date("Y-m-d H:i:s")
    ];
    switch (strtolower($data["auth-act"])) {
        case 'accept':
            $UPDATE_DATA["DPWD_STSVER"]  = -1;
            break;
        
        case 'reject':
            $UPDATE_DATA["DPWD_STSVER"]    = 1;
            $UPDATE_DATA["DPWD_STS"]       = 1;
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
            'message'   => "Failed to ".$data["auth-act"],
            'data'      => []
        ]);
    }

    if($UPDATE_DATA['DPWD_STSVER'] == 1) {
        /** Notifikasi email withdrawal reject */
        $emailData = [
            'subject' => "Konfirmasi Withdrawal Anda Telah Ditolak",
            'jumlah' => $RSLT_CHECK['DPWD_CURR_FROM'] . " " . Helper::formatCurrency($RSLT_CHECK['DPWD_AMOUNT_SOURCE']),
            'note' => $data['note']
        ];

        $emailSender = EmailSender::init(['email' => $userdata['MBR_EMAIL'], 'name' => $userdata['MBR_NAME']]);
        $emailSender->useFile("withdrawal-reject", $emailData);
        $send = $emailSender->send();
    }
    
    Logger::admin_log([
        'admid' => $user['ADM_ID'],
        'module' => "transaction/withdrawal/authorization",
        'message' => strtoupper($data["auth-act"])." Withdrawal",
        'data'  => $data
    ]);

    JsonResponse([
        'code'      => 200,
        'success'   => true,
        'message'   => "Success ".$data["auth-act"],
        'data'      => []
    ]);